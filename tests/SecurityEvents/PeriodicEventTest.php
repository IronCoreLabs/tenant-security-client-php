<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use PHPUnit\Framework\TestCase;

final class PeriodicEventTest extends TestCase
{
    public function testEventExpectedValues(): void
    {
        $this->assertEquals(PeriodicEvent::enforceRetentionPolicy()->getFlatEvent(), "PERIODIC_ENFORCE_RETENTION_POLICY");
        $this->assertEquals(PeriodicEvent::createBackup()->getFlatEvent(), "PERIODIC_CREATE_BACKUP");
    }
}
