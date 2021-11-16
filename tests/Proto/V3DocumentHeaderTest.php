<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use PHPUnit\Framework\TestCase;
use Proto\IronCoreLabs\DataControlPlatformHeader;
use Proto\IronCoreLabs\SaaSShieldHeader;
use Proto\IronCoreLabs\V3DocumentHeader;

final class V3DocumentHeaderTest extends TestCase
{
    public function testDcpHeader(): void
    {
        $documentId = "ID";
        $segmentId = 421;
        $dcp = new DataControlPlatformHeader();
        $dcp->setDocumentId($documentId);
        $dcp->setSegmentId($segmentId);
        $sig = "signature";
        $header = new V3DocumentHeader();
        $header->setDataControl($dcp);
        $header->setSig($sig);
        $this->assertEquals($header->getSig(), $sig);
        $this->assertEquals($header->getDataControl(), $dcp);
        $this->assertTrue($header->hasDataControl());
        $this->assertEquals($header->getHeader(), "data_control");
    }

    public function testSaasShieldHeader(): void
    {
        $tenantId = "tenant";
        $shield = new SaaSShieldHeader();
        $shield->setTenantId($tenantId);
        $sig = "signature";
        $header = new V3DocumentHeader();
        $header->setSaasShield($shield);
        $header->setSig($sig);
        $this->assertEquals($header->getSig(), $sig);
        $this->assertEquals($header->getSaasShield(), $shield);
        $this->assertTrue($header->hasSaasShield());
        $this->assertEquals($header->getHeader(), "saas_shield");
    }
}
