<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\Alphabet;
use HylianShield\Alphabet\AlphabetInterface;

/**
 * @see http://www.crockford.com/wrmg/base32.html
 */
class Base32CrockfordValidator extends Base32Validator
{
    /**
     * Constructor.
     *
     * @param bool $allowPartitions
     */
    public function __construct($allowPartitions = true)
    {
        parent::__construct(true, $allowPartitions);
    }

    /**
     * Get the padding character.
     *
     * If the bit-length of the number to be encoded is not a multiple of 5 bits,
     * then zero-extend the number to make its bit-length a multiple of 5.
     *
     * @return string
     */
    public function getPaddingCharacter(): string
    {
        return '0';
    }

    /**
     * Get the string sequence that denotes a partition in the encoded string.
     *
     * @return string
     */
    public function getPartitionSeparator(): string
    {
        return '-';
    }

    /**
     * Get the name of the message definition.
     *
     * @return string
     */
    public function getName(): string
    {
        return sprintf('%scrockford', parent::getName());
    }

    /**
     * Create the alphabet for the concrete implementation.
     *
     * Implementations of the decoder are restricted to a limited alphabet,
     * however, to improve support for decoding based on user input, an extended
     * alphabet is supported when decoding.
     * Therefore, a given string must be valid when it passes against the
     * extended alphabet.
     *
     * @return AlphabetInterface
     */
    protected function createAlphabet(): AlphabetInterface
    {
        return new Alphabet(
            '0Oo',
            '1IiLl',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'Aa',
            'Bb',
            'Cc',
            'Dd',
            'Ee',
            'Ff',
            'Gg',
            'Hh',
            'Jj',
            'Kk',
            'Mm',
            'Nn',
            'Pp',
            'Qq',
            'Rr',
            'Ss',
            'Tt',
            'Vv',
            'Ww',
            'Xx',
            'Yy',
            'Zz'
        );
    }

    /**
     * Validate the incoming message against the message definition.
     *
     * @param string $message
     *
     * @return bool
     */
    protected function validateMessage(string $message): bool
    {
        return parent::validateMessage(
            // Strip off instances of a trailing check symbol.
            // The check symbol will not be validated, as that requires a
            // complete decoding of the message.
            preg_replace(
                '/[\*\~\$\=Uu]?$/',
                '',
                $message
            )
        );
    }
}
