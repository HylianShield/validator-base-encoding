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
     * @param bool $allowCRLF
     */
    public function __construct(bool $allowCRLF = false)
    {
        // @see https://tools.ietf.org/html/rfc4648#section-8
        parent::__construct(false, $allowCRLF);
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
     * @see    http://tools.ietf.org/html/rfc4648#section-6
     */
    public function getPaddingCharacter(): string
    {
        return '';
    }

    /**
     * The amount of characters required to represent an encoding group.
     *
     * @return int
     */
    public function getGroupSize(): int
    {
        return 2;
    }
}
