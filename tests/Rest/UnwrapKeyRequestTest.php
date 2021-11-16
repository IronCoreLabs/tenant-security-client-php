<?php

declare(strict_types=1);

namespace IronCore\Rest;

use IronCore\Bytes;
use IronCore\IclFields;
use IronCore\RequestMetadata;
use PHPUnit\Framework\TestCase;

final class UnwrapKeyRequestTest extends TestCase
{
    public function testJsonData(): void
    {
        $iclFields = new IclFields("requesting-id");
        $metadata = new RequestMetadata("my-tenant", $iclFields, []);
        $edek = new Bytes("bar");
        $request = new UnwrapKeyRequest($metadata, $edek);
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
                "encryptedDocumentKey":"YmFy"
            }';
        // Generated JSON won't have extra lines or spaces
        $strippedExpected = str_replace(["\n", " "], "", $expected);
        $this->assertEquals($post, $strippedExpected);
    }
}
