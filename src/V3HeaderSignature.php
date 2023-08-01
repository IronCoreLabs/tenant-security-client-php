<?php

declare(strict_types=1);

namespace IronCore;

use IronCore\Crypto\AesConstants;
use UnexpectedValueException;

/**
 * V3 IronCore header signature
 */
final class V3HeaderSignature
{
    /**
     * @var Bytes
     */
    private $iv;
    /**
     * @var Bytes
     */
    private $gcmTag;

    /**
     * @param Bytes $iv Initialization vector
     * @param Bytes $gcmTag GCM tag
     */
    public function __construct(Bytes $iv, Bytes $gcmTag)
    {
        $this->iv = $iv;
        $this->gcmTag = $gcmTag;
    }

    /**
     * Constructs header signature from raw bytes.
     *
     * @param Bytes $bytes Raw signature bytes
     *
     * @return V3HeaderSignature V3 IronCore header signature
     */
    public static function fromBytes(Bytes $bytes): V3HeaderSignature
    {
        if ($bytes->length() != AesConstants::IV_LEN + AesConstants::TAG_LEN) {
            throw new
                UnexpectedValueException("Bytes were not a V3HeaderSignature because they were not the correct length");
        }

        return new V3HeaderSignature(
            $bytes->byteSlice(0, AesConstants::IV_LEN),
            $bytes->byteSlice(AesConstants::IV_LEN, AesConstants::TAG_LEN)
        );
    }

    /**
     * Gets the header's IV.
     *
     * @return Bytes Header IV
     */
    public function getIv(): Bytes
    {
        return $this->iv;
    }

    /**
     * Gets IronCore signature bytes. Consists of the IV concatenated with the GCM tag.
     *
     * @return Bytes IronCore signature bytes
     */
    public function getSignatureBytes(): Bytes
    {
        return $this->iv->concat($this->gcmTag);
    }
}
