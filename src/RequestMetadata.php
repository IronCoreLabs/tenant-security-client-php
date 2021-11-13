<?php

declare(strict_types=1);

namespace IronCore;

/**
 * Holds metadata fields as part of an encrypted document. Each encrypted document will have
 * metadata that associates it to a tenant ID, which service is accessing the data, 
 * as well as optional fields for other arbitrary key/value pairs and a request ID
 * to send to the Tenant Security Proxy.
 */
class RequestMetadata
{
    /**
     * @var string Unique ID of tenant that is performing the operation
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
            "tenantId" => $this->tenantId, "iclFields" => $this->iclFields,
            "customFields" => $this->customFields
        ];
    }
}
