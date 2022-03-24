<?php

declare(strict_types=1);

namespace IronCore\Rest;

use PHPUnit\Framework\TestCase;

final class BatchUnwrapKeyResponseTest extends TestCase
{

    public function testFromResponseGoodValue(): void
    {
        $dekString = "CDqKOqaO9x8WHr31bUNamvDcOpDUbSYUsGdJnmy2uIU=";
        $str = '{"keys":{"a":{"dek":"CDqKOqaO9x8WHr31bUNamvDcOpDUbSYUsGdJnmy2uIU="},"b":{"dek":"969SxKXGwZ2j9ypXBCJ7VjT1TM9Q49U9H48W2SLsOXM="}},"failures":{"bad":{"message":"Provided EDEK didn\'t contain IronCore EDEKs","code":203}}}';
        $result = BatchUnwrapKeyResponse::fromResponse($str);
        $this->assertEquals($result->getKeys()["a"]->getDek()->getBase64String(), $dekString);
        $this->assertEquals(count($result->getFailures()), 1);
    }
}
