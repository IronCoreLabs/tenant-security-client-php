<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\IclFields;
use IronCore\RequestMetadata;
use PHPUnit\Framework\TestCase;

final class RekeyRequestTest extends TestCase
{
    public function testJsonData(): void
    {
        $iclFields = new IclFields("requesting-id");
        $metadata = new RequestMetadata("my-tenant", $iclFields, []);
        $edek = new Bytes("bar");
        $newTenantId = "foo";
        $request = new RekeyRequest($metadata, $edek, $newTenantId);
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
                "encryptedDocumentKey":"YmFy",
                "newTenantId":"foo"
            }';
        // Generated JSON won't have extra lines or spaces
        $strippedExpected = str_replace(["\n", " "], "", $expected);
        $this->assertEquals($post, $strippedExpected);
    }
}
