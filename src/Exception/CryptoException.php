<?php

declare(strict_types=1);

namespace IronCore\Exception;

use Exception;

/**
 * This exception indicates a problem with encrypting, decrypting or verifying signatures.
 */
class CryptoException extends TenantSecurityException
{
}
