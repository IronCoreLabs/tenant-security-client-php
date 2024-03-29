<?php

declare(strict_types=1);

namespace IronCore\Exception;

/**
 * This exception indicates a problem with encrypting, decrypting or verifying signatures.
 */
final class CryptoException extends TenantSecurityException
{
}
