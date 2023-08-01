<?php

declare(strict_types=1);

namespace IronCore\Crypto;

// Starting in php 8.2, these constants can be in the Aes trait. Currently our minimum
// supported version is 7.4, so this workaround is necessary.
final class AesConstants
{
    public const IV_LEN = 12;
    public const TAG_LEN = 16;
    /** The size of the fixed length portion of the header (version, magic, size) */
    public const DOCUMENT_HEADER_META_LENGTH = 7;
    /** Max IronCore header size. Equals 256 * 255 + 255 since we do a 2 byte size. */
    public const MAX_HEADER_SIZE = 65535;
}
