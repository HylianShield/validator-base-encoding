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
     * @return int[]
     */
    public function getMaximumPaddingOccurrences(): array
    {
        return [6, 4, 3, 1];
    }

    /**
     * The amount of characters required to represent an encoding group.
     *
     * @return int
     */
    public function getGroupSize(): int
    {
        return 8;
    }
}
