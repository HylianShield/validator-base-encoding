<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see https://tools.ietf.org/html/rfc4648#section-7
 */
class Base32HexValidator extends Base32Validator
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
                range('0', '9'),
                range('A', 'V')
            )
        );
    }

    /**
     * Get the name of the message definition.
     *
     * @return string
     */
    public function getName(): string
    {
        return sprintf('%shex', parent::getName());
    }
}
