<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see https://tools.ietf.org/html/rfc4648#section-6
 */
class Base32Validator extends AbstractEncodingValidator
{
    /**
     * Create the alphabet for the concrete implementation.
     *
     * @return AlphabetInterface
     */
    protected function createAlphabet(): AlphabetInterface
    {
        return new Alphabet(
            ...array_merge(
                range('A', 'Z'),
                range('2', '7')
            )
        );
    }

    /**
     * Get the maximum amount of occurrences for the padding character in any
     * given message.
     *
     * Special processing is performed if fewer than 40 bits are available
     * at the end of the data being encoded.  A full encoding quantum is
     * always completed at the end of a body.  When fewer than 40 input bits
     * are available in an input group, bits with value zero are added (on
     * the right) to form an integral number of 5-bit groups.  Padding at
     * the end of the data is performed using the "=" character.  Since all
     * base 32 input is an integral number of octets, only the following
     * cases can arise:
     *
     * (1) The final quantum of encoding input is an integral multiple of 40
     * bits; here, the final unit of encoded output will be an integral
     * multiple of 8 characters with no "=" padding.
     *
     * (2) The final quantum of encoding input is exactly 8 bits; here, the
     * final unit of encoded output will be two characters followed by
     * six "=" padding characters.
     *
     * (3) The final quantum of encoding input is exactly 16 bits; here, the
     * final unit of encoded output will be four characters followed by
     * four "=" padding characters.
     *
     * (4) The final quantum of encoding input is exactly 24 bits; here, the
     * final unit of encoded output will be five characters followed by
     * three "=" padding characters.
     *
     * (5) The final quantum of encoding input is exactly 32 bits; here, the
     * final unit of encoded output will be seven characters followed by
     * one "=" padding character.
     *
     * @return int[]
     */
    public function getMaximumPaddingOccurrences(): array
    {
        return [6, 4, 3, 1];
    }

    /**
     * The amount of characters required to represent an encoding group.
     *
     * The encoding process represents 40-bit groups of input bits as output
     * strings of 8 encoded characters.  Proceeding from left to right, a
     * 40-bit input group is formed by concatenating 5 8bit input groups.
     * These 40 bits are then treated as 8 concatenated 5-bit groups, each
     * of which is translated into a single character in the base 32
     * alphabet.
     *
     * @return int
     */
    public function getGroupSize(): int
    {
        return 8;
    }
}
