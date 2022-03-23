<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Crypto\Aes;
use IronCore\Crypto\CryptoRng;

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
        $callback = fn (Bytes $field): Bytes => Aes::encryptDocument(
            $field,
            $tenantId,
            $dek,
            CryptoRng::getInstance()
        );
        $encryptedFields = array_map($callback, $document);
        return new EncryptedDocument($encryptedFields, $wrapResponse->getEdek());
    }

    public function batchEncrypt(array $documents, RequestMetadata $metadata): BatchEncryptedDocuments
    {
        // array_keys() turns numeric document IDs into ints, so we have to cast them back to strings.
        $documentIds = array_map(fn ($s): string => (string)$s, array_keys($documents));
        // Ask the TSP to generate a DEK/EDEK for each document ID
        $batchWrapKeyResponse = $this->request->batchWrapKeys($documentIds, $metadata);
        $keys = $batchWrapKeyResponse->getKeys();
        $keyFailures = $batchWrapKeyResponse->getFailures();
        $encryptedDocuments = [];
        $encryptionFailures = [];
        $tenantId = $metadata->getTenantId();

        foreach ($keyFailures as $documentId => $failure) {
            $encryptionFailures[$documentId] = $failure;
        };
        foreach ($keys as $documentId => $key) {
            $dek = $key->getDek();
            $edek = $key->getEdek();
            $document = $documents[$documentId];
            $encryptDocumentFields = fn (Bytes $fieldData): Bytes => Aes::encryptDocument(
                $fieldData,
                $tenantId,
                $dek,
                CryptoRng::getInstance()
            );
            $encryptedData = array_map($encryptDocumentFields, $document);
            $encryptedDocuments[$documentId] = new EncryptedDocument($encryptedData, $edek);
        };
        return new BatchEncryptedDocuments($encryptedDocuments, $encryptionFailures);
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
        $callback = fn (Bytes $field): Bytes => Aes::decryptDocument($field, $dek);
        $decryptedFields = array_map($callback, $encryptedFields);
        return new PlaintextDocument($decryptedFields, $document->getEdek());
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
}
