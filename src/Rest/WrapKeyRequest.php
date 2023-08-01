<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

/**
 * Request to the Tenant Security Proxy's wrap key endpoint
 */
final class WrapKeyRequest extends IronCoreRequest
{
    /**
     * @var RequestMetadata
     */
    private $metadata;

    /**
     * @param RequestMetadata $metadata Metadata about the unwrap key request
     */
    public function __construct(RequestMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getPostData(): array
    {
        return $this->metadata->getPostData();
    }
}
