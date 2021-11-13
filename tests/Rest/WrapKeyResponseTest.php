<?php

declare(strict_types=1);

namespace IronCore\Rest;

use PHPUnit\Framework\TestCase;

final class WrapKeyResponseTest extends TestCase
{

    public function testFromResponseGoodValue(): void
    {
        $dekString = "ZW9hdWV1b2FldW9hdW9lYQ==";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "{\"dek\":\"$dekString\", \"edek\": \"$edekString\"}";
        $result = WrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getDek()->getByteString(), base64_decode($dekString));
        $this->assertEquals($result->getEdek()->getByteString(), base64_decode($edekString));
    }

    public function testFromResponseBadDekValue(): void
    {
        $dekString = "9999 Not Base 64.";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "{\"dek\":\"$dekString\", \"edek\": \"$edekString\"}";
        $this->expectException(\InvalidArgumentException::class);
        $result = WrapKeyResponse::fromResponse($str);
    }

    public function testFromResponseBadJson(): void
    {
        $dekString = "9999 Not Base 64.";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "{\"dek\":\"$dekString\", \"edek\": \"$edekString\""; //Missing closing }
        $this->expectException(\InvalidArgumentException::class);
        $result = WrapKeyResponse::fromResponse($str);
    }

    public function testFromResponseJsonStringResponse(): void
    {
        $dekString = "9999 Not Base 64.";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "\"this is valid json string\"";
        $this->expectException(\InvalidArgumentException::class);
        $result = WrapKeyResponse::fromResponse($str);
    }
}
