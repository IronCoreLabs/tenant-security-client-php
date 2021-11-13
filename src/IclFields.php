<?php

declare(strict_types=1);

namespace IronCore;

class IclFields
{
    public $requestingId;
    public $dataLabel;
    public $sourceIp;
    public $objectId;
    public $requestId;

    public function __construct(
        string $requestingId,
        string $dataLabel = null,
        string $sourceIp = null,
        string $objectId = null,
        string $requestId = null
    ) {
        $this->requestingId = $requestingId;
        $this->dataLabel = $dataLabel;
        $this->sourceIp = $sourceIp;
        $this->objectId = $objectId;
        $this->requestId = $requestId;
    }
}
