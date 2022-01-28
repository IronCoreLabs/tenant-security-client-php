<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

/**
 * Request to the Tenant Security Proxy's re-key endpoint
 */
class RekeyRequest extends IronCoreRequest
{
    /**
     * @var RequestMetadata
     */
    private $metadata;
    /**
     * @var Bytes
     */
    private $edek;
    /**
     * @var string
     */
    private $newTenantId;

    /**
     * @param RequestMetadata $metadata Metadata about the re-key request
     * @param Bytes $edek Encrypted document key to re-key
     * @param string $newTenantId Tenant ID the document should be re-keyed to
     */
    public function __construct(RequestMetadata $metadata, Bytes $edek, string $newTenantId)
    {
        $this->metadata = $metadata;
        $this->edek = $edek->getBase64String();
        $this->newTenantId = $newTenantId;
    }

    public function getPostData(): array
    {
        $postData = $this->metadata->getPostData();
        $postData["encryptedDocumentKey"] = $this->edek;
        $postData["newTenantId"] = $this->newTenantId;
        return $postData;
    }
}
