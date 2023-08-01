<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

/**
 * Request to the Tenant Security Proxy's batch unwrap keys endpoint
 */
final class BatchUnwrapKeyRequest extends IronCoreRequest
{
    /**
     * @var RequestMetadata
     */
    private $metadata;
    /**
     * @var Bytes[]
     */
    private $edeks;

    /**
     * @param RequestMetadata $metadata Metadata about the batch unwrap key request
     * @param Bytes $edek Encrypted document keys to unwrap
     */
    public function __construct(RequestMetadata $metadata, array $edeks)
    {
        $this->metadata = $metadata;
        $bytesToString = fn (Bytes $bytes): string => $bytes->getBase64String();
        $stringEdeks = array_map($bytesToString, $edeks);
        $this->edeks = $stringEdeks;
    }

    public function getPostData(): array
    {
        $postData = $this->metadata->getPostData();
        $postData["edeks"] = $this->edeks;
        return $postData;
    }
}
