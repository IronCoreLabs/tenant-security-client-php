<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use PHPUnit\Framework\TestCase;

final class BatchPlaintextDocumentsTest extends TestCase
{
    public function testGetters(): void
    {
        $documents = [new PlaintextDocument(["foo" => new Bytes("bar")], new Bytes("edek"))];
        $failures = [new TenantSecurityException("failed", 100)];
        $batchDocuments = new BatchPlaintextDocuments($documents, $failures);
        $this->assertEquals($batchDocuments->getPlaintextDocuments(), $documents);
        $this->assertEquals($batchDocuments->getFailures(), $failures);
    }
}
