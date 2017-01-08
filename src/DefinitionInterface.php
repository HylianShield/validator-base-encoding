<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\AlphabetInterface;

interface DefinitionInterface
{
    /**
     * Get the name of the message definition.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * The padding character used to create a fixed recurring byte size in
     * base encoded messages.
     *
     * @return string
     */
    public function getPaddingCharacter(): string;

    /**
     * Get the maximum amount of occurrences for the padding character in any
     * given message.
     *
     * @return int[]
     */
    public function getMaximumPaddingOccurrences(): array;

    /**
     * The amount of characters required to represent an encoding group.
     *
     * @return int
     */
    public function getGroupSize(): int;

    /**
     * Get a list of allowed characters in the base encoding alphabet.
     *
     * @return AlphabetInterface
     */
    public function getAlphabet(): AlphabetInterface;

    /**
     * Whether the encoded string is allowed to be partitioned.
     *
     * @return bool
     */
    public function isPartitioningAllowed(): bool;

    /**
     * Get the string sequence that denotes a partition in the encoded string.
     *
     * @return string
     */
    public function getPartitionSeparator(): string;

    /**
     * Tells whether the padding in a message is required.
     *
     * @return bool
     */
    public function isPaddingRequired(): bool;
}
