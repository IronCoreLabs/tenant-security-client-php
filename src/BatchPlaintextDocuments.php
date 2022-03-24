<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A batch of documents which have been decrypted.
 */
class BatchPlaintextDocuments
{
    /**
     * @var PlaintextDocument[]
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
     * @return PlaintextDocument[] Successfully decrypted documents
     */
    public function getPlaintextDocuments(): array
    {
        return $this->documents;
    }

    /**
     * @return TenantSecurityException[] Documents which failed to be decrypted
     */
    public function getFailures(): array
    {
        return $this->failures;
    }
}
