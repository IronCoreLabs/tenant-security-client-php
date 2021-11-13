<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Crypto\Aes;
use IronCore\Crypto\CryptoRng;

class TenantSecurityClient
{
    /**
     * @var TenantSecurityRequest
     */
    public $request;

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
     * @return Encrypted Encrypted document and encrypted document key (EDEK)
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
}
