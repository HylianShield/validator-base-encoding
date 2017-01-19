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
    /** @var string[] */
    const EXTENDED_ALPHABET = ['*', '~', '$', '=', 'Uu'];

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
     * Get the maximum amount of occurrences for the padding character in any
     * given message.
     *
     * Because padding is normally done from right to left and the current
     * encoder uses right to left, the padding will not be trimmed by the
     * validator.
     * Since padding is required, regardless, the following always suffices,
     */
    public function getMaximumPaddingOccurrences(): array
    {
        return range(0, $this->getGroupSize());
    }

    /**
     * Trim the padding from a given message.
     *
     * @param string $message
     *
     * @return string
     */
    protected function trimPadding(string $message): string
    {
        return ltrim($message, $this->getPaddingCharacter());
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
        static $pattern;

        if ($pattern === null) {
            $alphabet = array_merge(
                iterator_to_array($this->getAlphabet()),
                static::EXTENDED_ALPHABET
            );

            $pattern = sprintf(
                '/^(([%s]{%d})*)([%s]?)$/',
                preg_quote(
                    implode('', array_slice($alphabet, 0, 32)),
                    '/'
                ),
                $this->getGroupSize(),
                preg_quote(
                    implode('', $alphabet),
                    '/'
                )
            );
        }

        // Strip off instances of a trailing check symbol.
        // The check symbol will not be validated, as that requires a
        // complete decoding of the message.
        if (preg_match($pattern, $message, $matches)) {
            $message = next($matches);
        }

        return parent::validateMessage($message);
    }
}
