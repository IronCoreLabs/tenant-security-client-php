<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A decrypted document.
 */
class PlaintextDocument
{
    /**
     * @var Bytes[]
     */
    private $decryptedFields;
    /**
     * @var Bytes
     */
    private $edek;

    /**
     * @param Bytes[] $decryptedFields Decrypted fields of the document
     * @param Bytes $edek Encrypted document key
     */
    public function __construct(array $decryptedFields, Bytes $edek)
    {
        $this->decryptedFields = $decryptedFields;
        $this->edek = $edek;
    }

    /**
     * @return Bytes[] Array of decrypted fields in raw byte form.
     */
    public function getDecryptedFields(): array
    {
        return $this->decryptedFields;
    }

    /**
     * @return Bytes The encrypted document key that was sent to the TSP for decryption.
     */
    public function getEdek(): Bytes
    {
        return $this->edek;
    }
}
