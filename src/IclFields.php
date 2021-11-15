<?php

declare(strict_types=1);

namespace IronCore;

/**
 * Holds metadata to pass to the Tenant Security Proxy for logging purposes.
 */
class IclFields
{
    /**
     * @var string (Required) Unique ID of user/service that is processing data
     */
    public $requestingId;
    /**
     * @var string (Optional) Classification of data being processed
     */
    public $dataLabel;
    /**
     * @var string (Optional) IP address of the initiator of this document request
     */
    public $sourceIp;
    /**
     * @var string (Optional) ID of the object/document being acted on in the host system
     */
    public $objectId;
    /**
     * @var string (Optional) Unique ID that ties application request ID to Tenant Security Proxy logs
     */
    public $requestId;

    /**
     * @param string $requestingId (Required) Unique ID of user/service that is processing data
     * @param string $dataLabel (Optional) Classification of data being processed
     * @param string $sourceIp (Optional) IP address of the initiator of this document request
     * @param string $objectId (Optional) ID of the object/document being acted on in the host system
     * @param string $requestId (Optional) Unique ID that ties application request ID to Tenant Security Proxy logs
     */
    public function __construct(
        string $requestingId,
        ?string $dataLabel = null,
        ?string $sourceIp = null,
        ?string $objectId = null,
        ?string $requestId = null
    ) {
        $this->requestingId = $requestingId;
        $this->dataLabel = $dataLabel;
        $this->sourceIp = $sourceIp;
        $this->objectId = $objectId;
        $this->requestId = $requestId;
    }
}
