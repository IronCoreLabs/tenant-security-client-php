<?php

declare(strict_types=1);

namespace IronCore;

class DocumentParts
{
    private $preamble;
    private $header;
    private $ciphertext;

    public function __construct(Bytes $preamble, Bytes $header, Bytes $ciphertext)
    {
        $this->preamble = $preamble;
        $this->header = $header;
        $this->ciphertext = $ciphertext;
    }

    public function getPreamble(): Bytes
    {
        return $this->preamble;
    }

    public function getHeader(): Bytes
    {
        return $this->header;
    }

    public function getCiphertext(): Bytes
    {
        return $this->ciphertext;
    }
}
