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
    private $isCRLFAllowed;

    /** @var AlphabetInterface */
    private $alphabet;

    /**
     * Constructor.
     *
     * @param bool $requirePadding
     * @param bool $allowCRLF
     */
    public function __construct(
        bool $requirePadding = true,
        bool $allowCRLF = false
    ) {
        $this->alphabet          = $this->createAlphabet();
        $this->isPaddingRequired = $requirePadding;
        $this->isCRLFAllowed     = $allowCRLF;

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
    public function isCRLFAllowed(): bool
    {
        return $this->isCRLFAllowed;
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
                $this->isCRLFAllowed()
                    ? 'CRLF'
                    : 'No-CRLF'
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
    public function validate($subject): bool
    {
        return (
            is_string($subject)
            && $this->validateMessage($subject)
        );
    }

    /**
     * Validate the incoming message against the message definition.
     *
     * @param string $message
     *
     * @return bool
     */
    private function validateMessage(string $message): bool
    {
        $message = $this->cleanMessageWhitespace($message);

        if ($this->isPaddingRequired()
            && !$this->validateByteLength($message)
        ) {
            return false;
        }

        return parent::validate(
            rtrim($message, $this->getPaddingCharacter())
        );
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

        return in_array(
            $this->getGroupSize() - strlen($finalGroup),
            $this->getMaximumPaddingOccurrences(),
            true
        );
    }

    /**
     * Clean the incoming message whitespace, based on rules in the given
     * definition.
     *
     * @param string $message
     *
     * @return string
     */
    private function cleanMessageWhitespace(string $message): string
    {
        $stripCharacters = [];

        if ($this->isCRLFAllowed()) {
            $stripCharacters[] = "\r\n";
        }

        if (count($stripCharacters) > 0) {
            $message = str_replace($stripCharacters, '', $message);
        }

        return $message;
    }
}
