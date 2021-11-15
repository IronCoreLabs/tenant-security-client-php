<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use PHPUnit\Framework\TestCase;

final class TenantSecurityRequestTest extends TestCase
{

    public function testFailedToMakeRequest(): void
    {
        $request = new TenantSecurityRequest("localhost:99999", "");
        $this->expectException(TenantSecurityException::class);
        $request->makeJsonRequest("/", "");
    }
}
