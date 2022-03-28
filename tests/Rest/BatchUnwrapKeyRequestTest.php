<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\IclFields;
use IronCore\RequestMetadata;
use PHPUnit\Framework\TestCase;

final class BatchUnwrapKeyRequestTest extends TestCase
{
    public function testJsonData(): void
    {
        $iclFields = new IclFields("requesting-id");
        $metadata = new RequestMetadata("my-tenant", $iclFields, []);
        $edek1 = new Bytes("foo");
        $edek2 = new Bytes("bar");
        $request = new BatchUnwrapKeyRequest($metadata, ["1" => $edek1, "b" => $edek2]);
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
                "edeks":{"1":"Zm9v","b":"YmFy"}
            }';
        // Generated JSON won't have extra lines or spaces
        $strippedExpected = str_replace(["\n", " "], "", $expected);
        $this->assertEquals($post, $strippedExpected);
    }
}
