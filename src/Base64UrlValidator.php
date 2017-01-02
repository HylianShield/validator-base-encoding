<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see https://tools.ietf.org/html/rfc4648#section-5
 */
class Base64UrlValidator extends Base64Validator
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
                range('a', 'z'),
                range('A', 'Z'),
                range('0', '9'),
                ['-', '_']
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
        return sprintf('%surl', parent::getName());
    }
}
