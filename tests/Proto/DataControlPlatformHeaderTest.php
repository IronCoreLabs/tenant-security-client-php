<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use PHPUnit\Framework\TestCase;
use Proto\IronCoreLabs\DataControlPlatformHeader;

final class DataControlPlatformHeaderTest extends TestCase
{
    public function testHeader(): void
    {
        $documentId = "ID";
        $segmentId = 421;
        $header = new DataControlPlatformHeader();
        $header->setDocumentId($documentId);
        $header->setSegmentId($segmentId);
        $headerDocumentId = $header->getDocumentId();
        $headerSegmentId = $header->getSegmentId();
        $this->assertEquals($documentId, $headerDocumentId);
        $this->assertEquals($segmentId, $headerSegmentId);
    }
}
