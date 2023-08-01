<?php

declare(strict_types=1);

namespace IronCore;

/**
 * RequestMetadata with an optional `timestampMillis` referencing when a security event took place.
 */
final class EventMetadata extends RequestMetadata
{
    /**
     * @var string Unique ID of tenant that is performing the operation.
     */
    private $tenantId;
    /**
     * @var IclFields Metadata about the request for the Tenant Security Proxy to log.
     */
    private $iclFields;
    /**
     * @var string[] Optional additional information for the Tenant Security Proxy to log.
     */
    private $customFields;
    /**
     * @var int Linux epoch millis of when the event occurred.
     */
    private $timestampMillis;

    /**
     * @param string $tenantId Unique ID of tenant that is performing the operation
     * @param IclFields $iclFields Metadata about the request for the Tenant Security Proxy to log
     * @param string[] $customFields Optional additional information for the Tenant Security Proxy to log
     * @param int $timestampMillis Linux epoch millis of when the event occurred.
     *            If this isn't passed, now will be assumed.
     */
    public function __construct(
        string $tenantId,
        IclFields $iclFields,
        array $customFields,
        int $timestampMillis = null
    ) {
        if ($timestampMillis == null) {
            $timestampMillis = intval(microtime(true) * 1000);
        };
        $this->tenantId = $tenantId;
        $this->iclFields = $iclFields;
        $this->customFields = $customFields;
        $this->timestampMillis = $timestampMillis;
    }

    /**
     * Gets the timestampMillis.
     *
     * @return int Linux epoch millis of when the event occurred.
     */
    public function getTimestampMillis()
    {
        return $this->timestampMillis;
    }

    /**
     * Gets an associative array of data that can be serialized
     * and sent to the Tenant Security Proxy.
     *
     * @return array Data to send to the Tenant Security Proxy.
     */
    public function getPostData(): array
    {
        return [
            "tenantId" => $this->tenantId,
            "iclFields" => $this->iclFields,
            "timestampMillis" => $this->timestampMillis,
            "customFields" => (object)$this->customFields // even if this is empty, it needs to serialize as an object.
        ];
    }
}
