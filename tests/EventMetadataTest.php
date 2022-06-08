<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class EventMetadataTest extends TestCase
{
    public function testEventMetadataPostData(): void
    {
        $tenantId = "my-tenant";
        $iclFields = new IclFields("foo");
        $metadata = new EventMetadata($tenantId, $iclFields, [], 123);
        $postData = $metadata->getPostData();
        $expected = [
            "tenantId" => "my-tenant", "iclFields" => new IclFields("foo"),
            "timestampMillis" => 123, "customFields" => (object)[]
        ];
        $this->assertEquals($postData, $expected);
    }

    public function testConstructionWithNullTimestamp(): void 
    {
        $tenantId = "my-tenant";
        $iclFields = new IclFields("foo");
        $metadata = new EventMetadata($tenantId, $iclFields, []);
        $this->assertNotNull($metadata->getTimestampMillis());
    }
}
