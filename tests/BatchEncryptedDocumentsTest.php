<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Exception\TenantSecurityException;
use PHPUnit\Framework\TestCase;

final class BatchEncryptedDocumentsTest extends TestCase
{
    public function testGetters(): void
    {
        $documents = [new EncryptedDocument(["foo" => new Bytes("bar")], new Bytes("edek"))];
        $failures = [new TenantSecurityException("failed", 100)];
        $batchDocuments = new BatchEncryptedDocuments($documents, $failures);
        $this->assertEquals($batchDocuments->getEncryptedDocuments(), $documents);
        $this->assertEquals($batchDocuments->getFailures(), $failures);
    }
}
