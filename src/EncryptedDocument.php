<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A set of fields which has been encrypted.
 */
class EncryptedDocument
{
    private $encryptedFields;
    private $edek;

    public function __construct(array $encryptedFields, Bytes $edek)
    {
        $this->encryptedFields = $encryptedFields;
        $this->edek = $edek;
    }

    /**
     * @return array The fields that have now been encrypted.
     */
    public function getEncryptedFields(): array
    {
        return $this->encryptedFields;
    }

    /**
     * @return Bytes An encrypted form of the DEK. This must be saved with the fields that
     *               were encrypted so it can be used to decrypt them later.
     */
    public function getEdek(): Bytes
    {
        return $this->edek;
    }
}
