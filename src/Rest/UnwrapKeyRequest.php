<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

/**
 * Request to the Tenant Security Proxy's unwrap key endpoint
 */
class UnwrapKeyRequest extends IronCoreRequest
{
    /**
     * @var RequestMetadata
     */
    private $metadata;
    /**
     * @var string
     */
    private $edek;

    /**
     * @param RequestMetadata $metadata Metadata about the unwrap key request
     * @param Bytes $edek Encrypted document key to unwrap
     */
    public function __construct(RequestMetadata $metadata, Bytes $edek)
    {
        $this->metadata = $metadata;
        $this->edek = $edek->getBase64String();
    }

    public function getPostData(): array
    {
        $postData = $this->metadata->getPostData();
        $postData["encryptedDocumentKey"] = $this->edek;
        return $postData;
    }
}
