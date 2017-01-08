<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see https://tools.ietf.org/html/rfc4648#section-8
 */
class Base16Validator extends AbstractEncodingValidator
{
    /**
     * Constructor.
     *
     * Unlike base 32 and base 64, no special padding is necessary since a
     * full code word is always available.
     *
     * @param bool $allowPartitions
     * @see   https://tools.ietf.org/html/rfc4648#section-8
     */
    public function __construct(bool $allowPartitions = false)
    {
        parent::__construct(false, $allowPartitions);
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
                range('0', '9'),
                range('A', 'F')
            )
        );
    }

    /**
     * Get the maximum amount of occurrences for the padding character in any
     * given message.
     *
     * @return int[]
     * @see    __construct
     */
    public function getMaximumPaddingOccurrences(): array
    {
        return [];
    }

    /**
     * The padding character used to create a fixed recurring byte size in
     * base encoded messages.
     *
     * @return string
     * @see    __construct
     */
    public function getPaddingCharacter(): string
    {
        return '';
    }

    /**
     * The amount of characters required to represent an encoding group.
     *
     * The encoding process represents 8-bit groups (octets) of input bits
     * as output strings of 2 encoded characters.  Proceeding from left to
     * right, an 8-bit input is taken from the input data.  These 8 bits are
     * then treated as 2 concatenated 4-bit groups, each of which is
     * translated into a single character in the base 16 alphabet.
     *
     * @return int
     */
    public function getGroupSize(): int
    {
        return 2;
    }
}
