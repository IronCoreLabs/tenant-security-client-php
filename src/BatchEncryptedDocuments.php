<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A batch of documents which have been encrypted.
 */
final class BatchEncryptedDocuments
{
    /**
     * @var EncryptedDocument[]
     */
    private $documents;
    /**
     * @var TenantSecurityException[]
     */
    private $failures;

    public function __construct(array $documents, array $failures)
    {
        $this->documents = $documents;
        $this->failures = $failures;
    }

    /**
     * @return EncryptedDocument[] Documents that were successfully encrypted
     */
    public function getEncryptedDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @return TenantSecurityException[] Documents that failed to be encrypted
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
