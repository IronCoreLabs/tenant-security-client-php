<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use PHPUnit\Framework\TestCase;

final class CryptoRngTest extends TestCase
{
    public function testGetInstance(): void
    {
        $instance1 = CryptoRng::getInstance();
        $instance2 = CryptoRng::getInstance();
        // Assert about deep equality
        $this->assertTrue($instance1 === $instance2);
    }

    public function testRandomBytesLength(): void
    {
        $rng = CryptoRng::getInstance();
        $bytes = $rng->randomBytes(12);
        $this->assertEquals(strlen($bytes->getByteString()), 12);
    }
}
