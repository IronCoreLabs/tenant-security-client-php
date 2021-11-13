<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\RequestMetadata;
use IronCore\Rest\IronCoreRequest;

class WrapKeyRequest extends IronCoreRequest
{
    private $metadata;

    public function __construct(RequestMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getPostData(): array
    {
        return $this->metadata->getPostData();
    }
}
