<?php

declare(strict_types=1);

namespace IronCore\Rest;

use PHPUnit\Framework\TestCase;

final class UnwrapKeyResponseTest extends TestCase
{

    public function testFromResponseGoodValue(): void
    {
        $dekString = "ZW9hdWV1b2FldW9hdW9lYQ==";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "{\"dek\":\"$dekString\", \"edek\": \"$edekString\"}";
        $result = UnwrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getDek()->getByteString(), base64_decode($dekString));
    }

    public function testFromResponseBadDekValue(): void
    {
        $dekString = "!hello world";
        $str = "{\"dek\":\"$dekString\"}";
        $this->expectException(\InvalidArgumentException::class);
        UnwrapKeyResponse::fromResponse($str);
    }

    public function testFromResponseBadJson(): void
    {
        $dekString = "hello world";
        $str = "{\"dek\":\"$dekString\""; //missing closing curly
        $this->expectException(\InvalidArgumentException::class);
        UnwrapKeyResponse::fromResponse($str);
    }
}
