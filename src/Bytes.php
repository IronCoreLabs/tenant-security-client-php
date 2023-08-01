<?php

declare(strict_types=1);

namespace IronCore;

use InvalidArgumentException;

/**
 * Wrapper around a string that contains raw bytes
 */
final class Bytes
{
    /**
     * @var string
     */
    private $bytes;

    public function __construct(string $bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * Constructs Bytes from a base64-encoded string. Holds the decoded bytes. If the input
     * is not valid base64 an InvalidArgumentException will be thrown.
     *
     * @return Bytes Bytes object holding the base64-decoded bytes
     */
    public static function fromBase64(string $base64String): Bytes
    {
        $decoded = base64_decode($base64String, true);
        if ($decoded == false) {
            throw new InvalidArgumentException("$base64String was not valid base64.");
        }
        return new Bytes($decoded);
    }

    /**
     * Gets the base64-encoded representation of the held bytes.
     *
     * @return string Base64-encoded string representing the held bytes
     */
    public function getBase64String(): string
    {
        return base64_encode($this->bytes);
    }

    /**
     * Printable hex representation of the held bytes. To return the held bytes
     * themselves, use `getByteString()`.
     *
     * @return string Hex representation of held bytes
     */
    public function getHexString(): string
    {
        return bin2hex($this->bytes);
    }

    /**
     * Printable hex representation of the held bytes. To return the held bytes
     * themselves, use `getByteString()`.
     *
     * @return string Hex representation of held bytes
     */
    public function __toString(): string
    {
        return $this->getHexString();
    }

    /**
     * Gets the length of the held bytes.
     *
     * @return int Length of held bytes
     */
    public function length(): int
    {
        return strlen($this->bytes);
    }

    /**
     * Gets a slice of the held bytes.
     *
     * Internally calls `substr` on the held bytes. Refer to its documentation
     * for more information.
     */
    public function byteSlice(int $offset, ?int $length = null): Bytes
    {
        if ($length === null) {
            return new Bytes(substr($this->bytes, $offset));
        } else {
            return new Bytes(substr($this->bytes, $offset, $length));
        };
    }

    /**
     * Gets the held byte string.
     *
     * This string is likely not printable. See `getHexString()` for a printable
     * representation of the bytes.
     *
     * @return string The held bytes
     */
    public function getByteString(): string
    {
        return $this->bytes;
    }

    /**
     * Concatenates the held bytes with the provided bytes.
     *
     * @param Bytes $other Bytes to go on the end of the held bytes
     *
     * @return Bytes The held bytes concatenated with the provided bytes
     */
    public function concat(Bytes $other): Bytes
    {
        return new Bytes($this->bytes . $other->getByteString());
    }

    /**
     * Gets the byte at the provided index.
     *
     * @param int $index Index to get the byte at
     *
     * @return Bytes The single byte located at the index
     */
    public function getAtIndex(int $index): Bytes
    {
        return new Bytes($this->bytes[$index]);
    }
}
