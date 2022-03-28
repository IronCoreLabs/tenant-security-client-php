<?php

declare(strict_types=1);

namespace IronCore\SecurityEvents;

use PHPUnit\Framework\TestCase;

final class DataEventTest extends TestCase
{
    public function testEventExpectedValues(): void
    {
        $this->assertEquals(DataEvent::import()->getFlatEvent(), "DATA_IMPORT");
        $this->assertEquals(DataEvent::export()->getFlatEvent(), "DATA_EXPORT");
        $this->assertEquals(DataEvent::encrypt()->getFlatEvent(), "DATA_ENCRYPT");
        $this->assertEquals(DataEvent::decrypt()->getFlatEvent(), "DATA_DECRYPT");
        $this->assertEquals(DataEvent::create()->getFlatEvent(), "DATA_CREATE");
        $this->assertEquals(DataEvent::delete()->getFlatEvent(), "DATA_DELETE");
        $this->assertEquals(DataEvent::denyAccess()->getFlatEvent(), "DATA_DENY_ACCESS");
        $this->assertEquals(DataEvent::changePermissions()->getFlatEvent(), "DATA_CHANGE_PERMISSIONS");
    }
}
