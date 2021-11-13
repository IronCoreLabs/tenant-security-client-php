<?php

declare(strict_types=1);

namespace IronCore\Utils;

/**
 * Trims forward slashes (`/`) from the front and end of a string.
 *
 * @param string $string String to trim
 *
 * @return string Trimmed string
 */
function trimSlashes(string $string): string
{
    return trim($string, "/");
}
