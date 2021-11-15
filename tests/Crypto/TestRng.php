<?php

declare(strict_types=1);

namespace IronCore\Crypto;

use IronCore\Bytes;

//This RNG produces truly random values, but predictably.
//It should only be used for testing, obviously.
final class TestRng extends Rng
{
    private $counter = 0;
    //Create a TestRng instance using $seed as the basis for all the hashed values.
    public function __construct(string $seed)
    {
        $this->seed = $seed;
    }

    public function randomBytes(int $length): Bytes
    {
        $bytes = "";
        while (strlen($bytes) <= $length) {
            $hashResult = hash("sha256", $this->seed . $this->counter++, true);
            $bytes =  $bytes . $hashResult;
        }
        return new Bytes(substr($bytes, 0, $length));
    }
}
