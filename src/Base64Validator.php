<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see https://tools.ietf.org/html/rfc4648#section-4
 */
class Base64Validator extends AbstractEncodingValidator
{
    /**
     * The maximum amount of padding characters at the end of an encoding.
     * Padding at the end of the data is performed using the '=' character.
     * Since all base 64 input is an integral number of octets, only the following
     * cases can arise:
     *
     * (1) The final quantum of encoding input is an integral multiple of 24
     *     bits; here, the final unit of encoded output will be an integral
     *     multiple of 4 characters with no "=" padding.
     *
     * (2) The final quantum of encoding input is exactly 8 bits; here, the
     *     final unit of encoded output will be two characters followed by
     *     two "=" padding characters.
     *
     * (3) The final quantum of encoding input is exactly 16 bits; here, the
     *     final unit of encoded output will be three characters followed by
     *     one "=" padding character.
     *
     * @return int[]
     */
    public function getMaximumPaddingOccurrences(): array
    {
        return [0, 2, 1];
    }

    /**
     * The amount of characters required to represent an encoding group.
     * The encoding process represents 24-bit groups of input bits as output
     * strings of 4 encoded characters.  Proceeding from left to right, a
     * 24-bit input group is formed by concatenating 3 8-bit input groups.
     * These 24 bits are then treated as 4 concatenated 6-bit groups, each
     * of which is translated into a single character in the base 64
     * alphabet.
     *
     * @return int
     */
    public function getGroupSize(): int
    {
        return 4;
    }

    /**
     * Create the alphabet for the concrete implementation.
     *
     * @return AlphabetInterface
     */
    protected function createAlphabet(): AlphabetInterface
    {
        return new Alphabet(
            ...array_merge(
                range('a', 'z'),
                range('A', 'Z'),
                range('0', '9'),
                ['+', '/']
            )
        );
    }
}
