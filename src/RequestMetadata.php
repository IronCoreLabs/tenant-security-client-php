<?php

declare(strict_types=1);

namespace IronCore;

/**
 * Holds metadata fields as part of an encrypted document. Each document has metadata associated with it
 * that will be sent to the Tenant Security Proxy for logging and other purposes. Some examples include
 * the tenant ID associated with the request, the service that is accessing the data, and a unique ID
 * for the request.
 */
class RequestMetadata
{
    /**
     * @var string Unique ID of tenant the action is being performed for
     */
    private $tenantId;
    /**
     * @var IclFields Metadata about the request for the Tenant Security Proxy to log
     */
    private $iclFields;
    /**
     * @var string[] Optional additional information for the Tenant Security Proxy to log
     */
    private $customFields;

    /**
     * @param string $tenantId Unique ID of tenant that is performing the operation
     * @param IclFields $iclFields Metadata about the request for the Tenant Security Proxy to log
     * @param string[] $customFields Optional additional information for the Tenant Security Proxy to log
     */
    public function __construct(string $tenantId, IclFields $iclFields, array $customFields)
    {
        $this->tenantId = $tenantId;
        $this->iclFields = $iclFields;
        $this->customFields = $customFields;
    }

    /**
     * Gets the ID of the tenant making the request.
     *
     * @return string Tenant ID making the request
     */
    public function getTenantId(): string
    {
        return $this->tenantId;
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
            "customFields" => (object)$this->customFields // even if this is empty, it needs to serialize as an object.
        ];
    }
}
