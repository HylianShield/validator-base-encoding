<?php
namespace HylianShield\Validator\BaseEncoding;

use HylianShield\Alphabet\AlphabetInterface;
use HylianShield\Validator\Alphabet\AlphabetValidator;

abstract class AbstractEncodingValidator extends AlphabetValidator implements
    DefinitionInterface
{
    /** @var string */
    private $identifier;

    /** @var bool */
    private $isPaddingRequired;

    /** @var bool */
    private $isPartitioningAllowed;

    /** @var AlphabetInterface */
    private $alphabet;

    /**
     * Constructor.
     *
     * @param bool $requirePadding
     * @param bool $allowPartitions
     */
    public function __construct(
        bool $requirePadding = true,
        bool $allowPartitions = false
    ) {
        $this->alphabet              = $this->createAlphabet();
        $this->isPaddingRequired     = $requirePadding;
        $this->isPartitioningAllowed = $allowPartitions;

        parent::__construct($this->alphabet);
    }

    /**
     * Create the alphabet for the concrete implementation.
     *
     * @return AlphabetInterface
     */
    abstract protected function createAlphabet(): AlphabetInterface;

    /**
     * Whether occurrences of carriage returns are allowed.
     *
     * @return bool
     */
    public function isPartitioningAllowed(): bool
    {
        return $this->isPartitioningAllowed;
    }

    /**
     * Tells whether the padding in a message is required.
     *
     * @return bool
     */
    public function isPaddingRequired(): bool
    {
        return $this->isPaddingRequired;
    }

    /**
     * Get a list of allowed characters in the base encoding alphabet.
     *
     * @return AlphabetInterface
     */
    public function getAlphabet(): AlphabetInterface
    {
        return $this->alphabet;
    }

    /**
     * The padding character used to create a fixed recurring byte size in
     * base encoded messages.
     *
     * @return string
     * @see    http://tools.ietf.org/html/rfc4648#section-3.2
     */
    public function getPaddingCharacter(): string
    {
        return '=';
    }

    /**
     * Get the string sequence that denotes a partition in the encoded string.
     *
     * @return string
     */
    public function getPartitionSeparator(): string
    {
        return "\r\n";
    }

    /**
     * Get the name of the message definition.
     *
     * @return string
     */
    public function getName(): string
    {
        return sprintf('base%d', count($this->alphabet));
    }

    /**
     * Get the validator identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        if ($this->identifier === null) {
            $this->identifier = sprintf(
                '%s(%s,%s)',
                $this->getName(),
                $this->isPaddingRequired()
                    ? 'require-padding'
                    : 'padding-optional',
                $this->isPartitioningAllowed()
                    ? 'partitioning'
                    : 'no-partitioning'
            );
        }

        return $this->identifier;
    }

    /**
     * Validate the incoming subject against the message definition.
     *
     * @param mixed $subject
     *
     * @return bool
     */
    final public function validate($subject): bool
    {
        if (!is_string($subject)) {
            return false;
        }

        return $this->validateMessage(
            $this->cleanMessagePartitioning($subject)
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
        if ($this->isPaddingRequired()
            && !$this->validateByteLength($message)
        ) {
            return false;
        }

        return parent::validate(
            $this->trimPadding($message)
        );
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
        return rtrim($message, $this->getPaddingCharacter());
    }

    /**
     * Validate the length of the message, including the padding.
     *
     * @param string $message
     *
     * @return bool
     */
    private function validateByteLength(string $message): bool
    {
        $length = strlen($message);

        if ($length % $this->getGroupSize() !== 0) {
            return false;
        }

        if ($length === 0) {
            return true;
        }

        $finalGroup = rtrim(
            substr(
                $message,
                -1 * $this->getGroupSize()
            ),
            $this->getPaddingCharacter()
        );

        return (
            empty($finalGroup)
            || in_array(
                $this->getGroupSize() - strlen($finalGroup),
                $this->getMaximumPaddingOccurrences(),
                true
            )
        );
    }

    /**
     * Clean the incoming message partitioning, based on rules in the given
     * definition.
     *
     * @param string $message
     *
     * @return string
     */
    private function cleanMessagePartitioning(string $message): string
    {
        if ($this->isPartitioningAllowed()) {
            $message = str_replace(
                $this->getPartitionSeparator(),
                '',
                $message
            );
        }

        return $message;
    }
}
