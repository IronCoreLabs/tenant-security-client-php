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

    public function testRequestMetadataPostData(): void
    {
        $tenantId = "my-tenant";
        $iclFields = new IclFields("foo");
        $metadata = new RequestMetadata($tenantId, $iclFields, [], 123);
        $postData = $metadata->getPostData();
        $expected = ["tenantId" => "my-tenant", "iclFields" => new IclFields("foo"), "customFields" => (object)[]];
        $this->assertEquals($postData, $expected);
    }
}
