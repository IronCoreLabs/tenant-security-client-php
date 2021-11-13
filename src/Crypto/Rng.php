<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use IronCore\Bytes;

// This class exists purely as a means of making functions which need RNG more testable.
// For all real code, CryptoRng should be used.
abstract class Rng
{
    abstract public function randomBytes(int $length): Bytes;
}
