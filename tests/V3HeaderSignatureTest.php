<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

final class V3HeaderSignatureTest extends TestCase
{
    public function testFromBytesFailure(): void
    {
        $badBytes = new Bytes("badBytes");
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Bytes were not a V3HeaderSignature because they were not the correct length");
        V3HeaderSignature::fromBytes($badBytes);
    }
}
