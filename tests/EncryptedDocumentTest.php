<?php

declare(strict_types=1);

namespace IronCore;

use PHPUnit\Framework\TestCase;

final class EncryptedDocumentTest extends TestCase
{
    public function testGetters(): void
    {
        $fields = ["foo" => new Bytes("bar")];
        $edek = new Bytes("edek");
        $document = new EncryptedDocument($fields, $edek);
        $this->assertEquals($document->getEdek(), $edek);
        $this->assertEquals($document->getEncryptedFields(), $fields);
    }
}
