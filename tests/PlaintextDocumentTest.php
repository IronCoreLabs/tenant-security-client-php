<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class PlaintextDocumentTest extends TestCase
{
    public function testGetters(): void
    {
        $fields = ["foo" => new Bytes("bar")];
        $edek = new Bytes("edek");
        $document = new PlaintextDocument($fields, $edek);
        $this->assertEquals($document->getEdek(), $edek);
        $this->assertEquals($document->getDecryptedFields(), $fields);
    }
}
