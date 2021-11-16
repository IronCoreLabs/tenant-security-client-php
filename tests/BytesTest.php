<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class BytesTest extends TestCase
{
    public function testBytesToHex(): void
    {
        $bytes = new Bytes("cow");
        $hex = $bytes->getHexString();
        $expected = "636f77";
        $this->assertEquals($hex, $expected);

        // Should get same result from toString implementation
        $toString = strval($bytes);
        $this->assertEquals($toString, $expected);
    }
}
