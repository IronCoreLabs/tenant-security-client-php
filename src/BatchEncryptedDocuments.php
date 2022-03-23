<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A batch of documents which have been encrypted.
 */
class BatchEncryptedDocuments
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
     * @return EncryptedDocument[] TODO
     */
    public function getEncryptedDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @return TenantSecurityException[]
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
