<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Crypto\Aes;
use UnexpectedValueException;

class V3HeaderSignature
{
    private $iv;
    private $gcmTag;
    public function __construct(Bytes $iv, Bytes $gcmTag)
    {
        $this->iv = $iv;
        $this->gcmTag = $gcmTag;
    }

    public function getIv(): Bytes
    {
        return $this->iv;
    }

    public static function fromBytes(Bytes $bytes): V3HeaderSignature
    {
        if ($bytes->length() != Aes::IV_LEN + Aes::TAG_LEN) {
            throw new
                UnexpectedValueException("Bytes were not a V3HeaderSignature because they were not the correct length");
        }

        return new V3HeaderSignature($bytes->byteSlice(0, Aes::IV_LEN), $bytes->byteSlice(Aes::IV_LEN, Aes::TAG_LEN));
    }

    public function getSig(): Bytes
    {
        return $this->iv->concat($this->gcmTag);
    }
}
