<?php

declare(strict_types=1);

namespace IronCore\Exception;

use PHPUnit\Framework\TestCase;

final class TenantSecurityExceptionTest extends TestCase
{
    public function testKMSExceptionCreateAndCode(): void
    {
        $exception = new KmsException("message", 1);
        $this->assertEquals($exception->getCode(), 1);
        $this->assertEquals($exception->getMessage(), "message");
    }

    public function testFromResponseUnknownForBadData(): void
    {
        $exception = TenantSecurityException::fromResponse("");
        $this->assertEquals($exception->getCode(), -1);
    }

    public function testFromResponseUnknownWhenMissingCode(): void
    {
        $exception = TenantSecurityException::fromResponse("[]");
        $this->assertEquals($exception->getCode(), -1);
    }


    public function testForAllKnownCodes(): void
    {
        $this->assertEquals(self::createExceptionForCode(0)->getCode(), 0);
        $this->assertEquals(self::createExceptionForCode(100)->getCode(), 100);
        $this->assertEquals(self::createExceptionForCode(101)->getCode(), 101);
        $this->assertEquals(self::createExceptionForCode(102)->getCode(), 102);
        $this->assertEquals(self::createExceptionForCode(200)->getCode(), 200);
        $this->assertEquals(self::createExceptionForCode(201)->getCode(), 201);
        $this->assertEquals(self::createExceptionForCode(202)->getCode(), 202);
        $this->assertEquals(self::createExceptionForCode(203)->getCode(), 203);
        $this->assertEquals(self::createExceptionForCode(204)->getCode(), 204);
        $this->assertEquals(self::createExceptionForCode(205)->getCode(), 205);
        $this->assertEquals(self::createExceptionForCode(206)->getCode(), 206);
        $this->assertEquals(self::createExceptionForCode(207)->getCode(), 207);
        $this->assertEquals(self::createExceptionForCode(208)->getCode(), 208);
        $this->assertEquals(self::createExceptionForCode(209)->getCode(), 209);
        $this->assertEquals(self::createExceptionForCode(301)->getCode(), 301);
    }

    public function testForUnknownCode(): void
    {
        $exception = self::createExceptionForCode(99999);
        $this->assertEquals($exception->getCode(), 99999);
        $this->assertEquals("Unknown TSP error code.", $exception->getMessage());
    }


    private static function createExceptionForCode(int $code): TenantSecurityException
    {
        return TenantSecurityException::fromResponse("{\"code\":$code}");
    }
}
