<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\IclFields;
use IronCore\RequestMetadata;
use PHPUnit\Framework\TestCase;

final class BatchWrapKeyRequestTest extends TestCase
{
    public function testJsonData(): void
    {
        $iclFields = new IclFields("requesting-id");
        $metadata = new RequestMetadata("my-tenant", $iclFields, []);
        $request = new BatchWrapKeyRequest($metadata, ["1", "b"]);
        $post = $request->getJsonData();
        $expected =
            '{
                "tenantId":"my-tenant",
                "iclFields":{
                    "requestingId":"requesting-id",
                    "dataLabel":null,
                    "sourceIp":null,
                    "objectId":null,
                    "requestId":null
                },
                "customFields":{},
                "documentIds":["1","b"]
            }';
        // Generated JSON won't have extra lines or spaces
        $strippedExpected = str_replace(["\n", " "], "", $expected);
        $this->assertEquals($post, $strippedExpected);
    }
}
