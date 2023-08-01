<?php

declare(strict_types=1);

namespace IronCore;

/**
 * A parsed IronCore encrypted document
 */
final class DocumentParts
{
    /**
     * @var Bytes
     */
    private $preamble;
    /**
     * @var Bytes
     */
    private $header;
    /**
     * @var Bytes
     */
    private $ciphertext;

    /**
     * @param Bytes $preamble The first 7 bytes of the document
     * @param Bytes $header The header of the document
     * @param Bytes $ciphertext The encrypted payload of the document
     */
    public function __construct(Bytes $preamble, Bytes $header, Bytes $ciphertext)
    {
        $this->preamble = $preamble;
        $this->header = $header;
        $this->ciphertext = $ciphertext;
    }

    /**
     * Gets the preamble of the document.
     *
     * @return Bytes Document preamble
     */
    public function getPreamble(): Bytes
    {
        return $this->preamble;
    }

    /**
     * Gets the header of the document.
     *
     * @return Bytes Document header
     */
    public function getHeader(): Bytes
    {
        return $this->header;
    }

    /**
     * Gets the encrypted payload of the document.
     *
     * @return Bytes Encrypted document payload
     */
    public function getCiphertext(): Bytes
    {
        return $this->ciphertext;
    }
}
