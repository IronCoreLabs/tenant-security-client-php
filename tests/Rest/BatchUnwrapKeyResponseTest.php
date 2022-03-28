<?php

declare(strict_types=1);

namespace IronCore\Rest;

use PHPUnit\Framework\TestCase;

final class BatchUnwrapKeyResponseTest extends TestCase
{

    public function testFromResponseGoodValue(): void
    {
        $dekString = "CDqKOqaO9x8WHr31bUNamvDcOpDUbSYUsGdJnmy2uIU=";
        $str = "{\"keys\":{\"a\":{\"dek\":\"$dekString\"}},\"failures\":{}}";
        $result = BatchUnwrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getKeys()["a"]->getDek()->getBase64String(), $dekString);
        $this->assertEquals(count($result->getFailures()), 0);
    }

    public function testFromResponseBadJson(): void
    {
        $str = '{bad'; // missing closing curly
        $this->expectException(\InvalidArgumentException::class);
        BatchUnwrapKeyResponse::fromResponse($str);
    }

    public function testFromResponseBadDek(): void
    {
        $dekString = "!bad";
        $str = "{\"keys\":{\"a\":{\"dek\":\"$dekString\"}},\"failures\":{}}";
        $this->expectException(\InvalidArgumentException::class);
        BatchUnwrapKeyResponse::fromResponse($str);
    }
}
