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
        $str = '{"keys":{"b":{"dek":"ZW9hdWV1b2FldW9hdW9lYQ==","edek":"ODc5NDMyODk3NDMyOTg3MjQzODk3"}},"failures":{}}';
        $result = BatchWrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getKeys()["b"]->getDek()->getBase64String(), $dekString);
        $this->assertEquals($result->getKeys()["b"]->getEdek()->getBase64String(), $edekString);
        $this->assertEquals(count($result->getFailures()), 0);
    }
}
