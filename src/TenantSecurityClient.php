<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Crypto\Aes;
use IronCore\Crypto\CryptoRng;
use IronCore\SecurityEvents\SecurityEvent;

/**
 * Client used to encrypt and decrypt documents. This is the primary class that consumers of the
 * library will need to utilize, and a single instance of the class can be re-used for requests
 * across different tenants.
 */
class TenantSecurityClient
{
    /**
     * @var TenantSecurityRequest
     */
    private $request;

    /**
     * @param string $tspAddress URL of the Tenant Security Proxy
     * @param string $apiKey Secret key needed to communicate with the Tenant Security Proxy
     */
    public function __construct(string $tspAddress, string $apiKey)
    {
        $this->request = new TenantSecurityRequest($tspAddress, $apiKey);
    }

    /**
     * Encrypts the provided document. Documents are provided as a map of fields from the document
     * id/name (string) to bytes (string). Uses the Tenant Security Proxy to generate a new document
     * encryption key (DEK), encrypt that key (EDEK), then use the DEK to encrypt all of the
     * provided document fields. Returns an EncryptedDocument which contains a Map from each field's
     * id/name to encrypted bytes as well as the EDEK and discards the DEK.
     *
     * @param Bytes[] $document Document to encrypt. Each field in the provided document will be encrypted
     *                        with the same key.
     * @param RequestMetadata $metadata Metadata about the document being encrypted
     *
     * @return EncryptedDocument Encrypted document and encrypted document key (EDEK)
     */
    public function encrypt(array $document, RequestMetadata $metadata): EncryptedDocument
    {
        $wrapResponse = $this->request->wrapKey($metadata);
        $tenantId = $metadata->getTenantId();
        $dek = $wrapResponse->getDek();
        $encryptDocument = fn (Bytes $field): Bytes => Aes::encryptDocument(
            $field,
            $tenantId,
            $dek,
            CryptoRng::getInstance()
        );
        $encryptedFields = array_map($encryptDocument, $document);
        return new EncryptedDocument($encryptedFields, $wrapResponse->getEdek());
    }

    public function encryptWithExistingKey(PlaintextDocument $document, RequestMetadata $metadata): EncryptedDocument
    {
        $unwrapResponse = $this->request->unwrapKey($document->getEdek(), $metadata);
        $tenantId = $metadata->getTenantId();
        $dek = $unwrapResponse->getDek();
        $encryptDocument = fn (Bytes $field): Bytes => Aes::encryptDocument(
            $field,
            $tenantId,
            $dek,
            CryptoRng::getInstance()
        );
        $encryptedFields = array_map($encryptDocument, $document->getDecryptedFields());
        return new EncryptedDocument($encryptedFields, $document->getEdek());
    }

    /**
     * Encrypts an array of documents from the ID of the document to the list of fields to encrypt.
     * Makes a call out to the Tenant Security Proxy to generate a collection of new DEK/EDEK pairs
     * for each document ID provided. This function supports partial failure so it returns two Maps,
     * one of document ID to successfully encrypted document and one of document ID to a TenantSecurityException.
     *
     * @param Bytes[][] $documents Documents to encrypt. Each entry in the array is [documentId => Bytes[]].
     * @param RequestMetadata $metadata Metadata about the documents being encrypted
     *
     * @return BatchEncryptedDocuments Collection of successes and failures that occurred during operation.
     *          The keys of each map returned will be the same keys provided in the original documents map.
     */
    public function batchEncrypt(array $documents, RequestMetadata $metadata): BatchEncryptedDocuments
    {
        // array_keys() turns numeric document IDs into ints, so we have to cast them back to strings.
        $documentIds = array_map(fn ($s): string => (string)$s, array_keys($documents));
        // Ask the TSP to generate a DEK/EDEK for each document ID
        $batchWrapKeyResponse = $this->request->batchWrapKeys($documentIds, $metadata);
        $keys = $batchWrapKeyResponse->getKeys();
        $keyFailures = $batchWrapKeyResponse->getFailures();
        $encryptedDocuments = [];
        $tenantId = $metadata->getTenantId();

        foreach ($keys as $documentId => $key) {
            $dek = $key->getDek();
            $edek = $key->getEdek();
            $document = $documents[$documentId];
            $encryptDocumentFields = fn (Bytes $field): Bytes => Aes::encryptDocument(
                $field,
                $tenantId,
                $dek,
                CryptoRng::getInstance()
            );
            $encryptedData = array_map($encryptDocumentFields, $document);
            $encryptedDocuments[$documentId] = new EncryptedDocument($encryptedData, $edek);
        };
        return new BatchEncryptedDocuments($encryptedDocuments, $keyFailures);
    }

    /**
     * Decrypts the provided EncryptedDocument. Decrypts the document's encrypted document key (EDEK)
     * using the Tenant Security Proxy and uses it to decrypt and return the document bytes. The DEK
     * is then discarded.
     *
     * @param EncryptedDocument $document Encrypted document to decrypt
     * @param RequestMetadata $metadata Metadata about the document being decrypted
     *
     * @return PlaintextDocument Decrypted document and encrypted document key (EDEK)
     */
    public function decrypt(EncryptedDocument $document, RequestMetadata $metadata): PlaintextDocument
    {
        $unwrapResponse = $this->request->unwrapKey($document->getEdek(), $metadata);
        $dek = $unwrapResponse->getDek();
        $encryptedFields = $document->getEncryptedFields();
        $decryptDocumentFields = fn (Bytes $field): Bytes => Aes::decryptDocument($field, $dek);
        $decryptedFields = array_map($decryptDocumentFields, $encryptedFields);
        return new PlaintextDocument($decryptedFields, $document->getEdek());
    }

    /**
     * Decrypts a map of documents from the ID of the document to its encrypted content. Makes a call
     * out to the Tenant Security Proxy to decrypt all of the EDEKs in each document. This function
     * supports partial failure so it returns two Maps, one of document ID to successfully decrypted
     * document and one of document ID to a TenantSecurityException.
     *
     * @param EncryptedDocument[] $documents Encrypted documents to decrypt. Each entry in the array is
     *                            [documentId => EncryptedDocument].
     * @param RequestMetadata Metadata about the documents being decrypted
     *
     * @return BatchPlaintextDocuments Collection of successes and failures that occurred during operation.
     *          The keys of each map returned will be the same keys provided in the original documents map.
     */
    public function batchDecrypt(array $documents, RequestMetadata $metadata): BatchPlaintextDocuments
    {
        // make map from docId => edek
        /**@var Bytes[] */
        $edeks = array_map(fn (EncryptedDocument $doc): Bytes => $doc->getEdek(), $documents);
        // Ask the TSP to unwrap the EDEK for each document ID
        $batchUnwrapKeyResponse = $this->request->batchUnwrapKeys($edeks, $metadata);
        $unwrappedKeys = $batchUnwrapKeyResponse->getKeys();
        $keyFailures = $batchUnwrapKeyResponse->getFailures();
        $decryptedDocuments = [];

        foreach ($unwrappedKeys as $documentId => $key) {
            $dek = $key->getDek();
            $edek = $edeks[$documentId];
            $document = $documents[$documentId];
            $decryptFields = fn (Bytes $field): Bytes => Aes::decryptDocument($field, $dek);
            $decryptedDocument = array_map($decryptFields, $document->getEncryptedFields());
            $decryptedDocuments[$documentId] = new PlaintextDocument($decryptedDocument, $edek);
        };
        return new BatchPlaintextDocuments($decryptedDocuments, $keyFailures);
    }

    /**
     * Re-key a document's encrypted document key (EDEK) to a new tenant. Decrypts the EDEK then re-encrypts it to the
     * new tenant. The DEK is then discarded. The old tenant and new tenant can be the same in order to re-key the
     * document to the tenant's latest primary config.
     *
     * @param Bytes $edek EDEK to re-key
     * @param string $newTenantId Tenant ID the document should be re-keyed to
     * @param RequestMetadata $metadata Metadata about the document being re-keyed.
     *
     * @return Bytes Newly re-keyed EDEK
     */
    public function rekeyEdek(Bytes $edek, string $newTenantId, RequestMetadata $metadata): Bytes
    {
        $rekeyResponse = $this->request->rekey($edek, $newTenantId, $metadata);
        return $rekeyResponse->getEdek();
    }

    /**
     * Send the provided security event to the TSP to be logged and analyzed. Note that logging a security event is an
     * asynchronous operation at the TSP, so successful receipt of a security event does not mean
     * that the event is deliverable or has been delivered to the tenant's logging system. It simply
     * means that the event has been received and will be processed.
     *
     * @param SecurityEvent $event Security event that represents the action that took place.
     * @param EventMetadata $metadata Metadata that provides additional context about the event.
     */

    public function logSecurityEvent(SecurityEvent $event, EventMetadata $metadata): void
    {
        $this->request->logSecurityEvent($event, $metadata);
    }
}
