<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

/**
 * Request to the Tenant Security Proxy's batch wrap keys endpoint
 */
final class BatchWrapKeyRequest extends IronCoreRequest
{
    /**
     * @var RequestMetadata
     */
    private $metadata;
    /**
     * @var string[]
     */
    private $documentIds;

    /**
     * @param RequestMetadata $metadata Metadata about the batch wrap key request
     * @param string[] $documentIds Document IDs to generate DEK/EDEK pairs for
     */
    public function __construct(RequestMetadata $metadata, array $documentIds)
    {
        $this->metadata = $metadata;
        $this->documentIds = $documentIds;
    }

    public function getPostData(): array
    {
        $postData = $this->metadata->getPostData();
        $postData["documentIds"] = $this->documentIds;
        return $postData;
    }
}
