<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

class UnwrapKeyRequest extends IronCoreRequest
{
    private $metadata;
    private $base64Edek;

    public function __construct(RequestMetadata $metadata, Bytes $edek)
    {
        $this->metadata = $metadata;
        $this->base64Edek = $edek->getBase64String();
    }

    public function getPostData(): array
    {
        $post_data = $this->metadata->getPostData();
        $post_data["encryptedDocumentKey"] = $this->base64Edek;
        return $post_data;
    }
}
