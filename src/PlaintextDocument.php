<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A decrypted document.
 */
class PlaintextDocument
{
    private $decryptedFields;
    private $edek;

    public function __construct(array $decryptedFields, Bytes $edek)
    {
        $this->decryptedFields = $decryptedFields;
        $this->edek = $edek;
    }

    /**
     * @return array of fields which are now decrypted. Each field is in raw byte form.
     */
    public function getDecryptedFields(): array
    {
        return $this->decryptedFields;
    }

    /**
     * @return Bytes The edek that was sent to the TSP for decryption.
     */
    public function getEdek(): Bytes
    {
        return $this->edek;
    }
}
