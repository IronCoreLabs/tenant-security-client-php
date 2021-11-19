<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use PHPUnit\Framework\TestCase;
use Proto\IronCoreLabs\SaaSShieldHeader;

final class SaasShieldHeaderTest extends TestCase
{
    public function testHeader(): void
    {
        $tenantId = "foo";
        $header = new SaaSShieldHeader();
        $header->setTenantId($tenantId);
        $headerTenantId = $header->getTenantId();
        $this->assertEquals($tenantId, $headerTenantId);
    }
}
