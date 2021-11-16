<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class RequestMetadataTest extends TestCase
{
    public function testRequestMetadataTenantId(): void
    {
        $tenantId = "my-tenant";
        $metadata = new RequestMetadata($tenantId, new IclFields("foo"), []);
        $this->assertEquals($metadata->getTenantId(), $tenantId);
    }
}
