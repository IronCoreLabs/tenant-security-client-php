<?php

declare(strict_types=1);

namespace IronCore\Rest;

use PHPUnit\Framework\TestCase;

final class BatchWrapKeyResponseTest extends TestCase
{

    public function testFromResponseGoodValue(): void
    {
        $dekString = "ZW9hdWV1b2FldW9hdW9lYQ==";
        $edekString = "ODc5NDMyODk3NDMyOTg3MjQzODk3";
        $str = "{\"keys\":{\"b\":{\"dek\":\"$dekString\",\"edek\":\"$edekString\"}},\"failures\":{}}";
        $result = BatchWrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getKeys()["b"]->getDek()->getBase64String(), $dekString);
        $this->assertEquals($result->getKeys()["b"]->getEdek()->getBase64String(), $edekString);
        $this->assertEquals(count($result->getFailures()), 0);
    }

    public function testFromResponseBadJson(): void
    {
        $str = '{bad'; // missing closing curly
        $this->expectException(\InvalidArgumentException::class);
        BatchWrapKeyResponse::fromResponse($str);
    }

    public function testFromResponseBadDekAndEdek(): void
    {
        $dekString = "!bad";
        $edekString = "!bad";
        $str = "{\"keys\":{\"b\":{\"dek\":\"$dekString\",\"edek\":\"$edekString\"}},\"failures\":{}}";
        $this->expectException(\InvalidArgumentException::class);
        BatchWrapKeyResponse::fromResponse($str);
    }
}
