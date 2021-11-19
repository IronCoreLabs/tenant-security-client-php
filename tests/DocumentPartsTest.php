<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class DocumentPartsTest extends TestCase
{
    public function testGetters(): void
    {
        $preamble = new Bytes("aaa");
        $header = new Bytes("bbb");
        $ciphertext = new Bytes("ccc");
        $parts = new DocumentParts($preamble, $header, $ciphertext);
        $this->assertEquals($parts->getPreamble(), $preamble);
        $this->assertEquals($parts->getHeader(), $header);
        $this->assertEquals($parts->getCiphertext(), $ciphertext);
    }
}
