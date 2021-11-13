<?php

declare(strict_types=1);

namespace IronCore;

class RequestMetadata
{
    private $tenant_id;
    private $icl_fields;
    private $custom_fields;

    public function __construct(string $tenant_id, IclFields $icl_fields, array $custom_fields)
    {
        $this->tenant_id = $tenant_id;
        $this->icl_fields = $icl_fields;
        $this->custom_fields = $custom_fields;
    }

    public function getTenantId(): string
    {
        return $this->tenant_id;
    }

    public function getPostData(): array
    {
        return [
            "tenantId" => $this->tenant_id, "iclFields" => $this->icl_fields,
            "customFields" => $this->custom_fields
        ];
    }
}
