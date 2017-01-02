<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\AbstractEncodingValidator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

abstract class BaseEncodingValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $identifierPattern = '/^base(64|32|16)(\w+)?\((require-)?padding(-optional)?,(No-)?CRLF\)$/';

    /** @var ReflectionClass */
    private $reflection;

    /**
     * @return int
     */
    abstract protected function getAlphabetSize(): int;

    /**
     * @return string
     */
    abstract protected function getValidatorClassName(): string;

    /**
     * @return string[][]
     */
    abstract public function messageWithPaddingProvider(): array;

    /**
     * @return string[][]
     */
    abstract public function messageWithCRLFProvider(): array;

    /**
     * @return ReflectionClass
     */
    final protected function getReflection(): ReflectionClass
    {
        if ($this->reflection === null) {
            $this->reflection = new ReflectionClass(
                $this->getValidatorClassName()
            );
        }

        return $this->reflection;
    }

    /**
     * @param bool $requirePadding
     * @param bool $allowCRLF
     *
     * @return AbstractEncodingValidator
     */
    protected function createValidator(
        bool $requirePadding = true,
        bool $allowCRLF = false
    ): AbstractEncodingValidator {
        /** @var AbstractEncodingValidator $validator */
        $validator = $this->getReflection()->newInstance(
            $requirePadding,
            $allowCRLF
        );

        return $validator;
    }

    /**
     * @param AbstractEncodingValidator $validator
     */
    private function assertIdentifier(
        AbstractEncodingValidator $validator
    ) {
        /** @noinspection PhpMethodOrClassCallIsNotCaseSensitiveInspection */
        $this->assertRegexp(
            $this->identifierPattern,
            $validator->getIdentifier()
        );
    }

    /**
     * @dataProvider messageWithPaddingProvider
     *
     * @param string $message
     *
     * @return void
     */
    public function testWithPaddingValidation(string $message)
    {
        // Require padding, disallow CRLF.
        $validator = $this->createValidator(true, false);
        $this->assertIdentifier($validator);

        $this->assertTrue(
            $validator->validate($message)
        );

        if (!empty($message) && strlen($validator->getPaddingCharacter()) > 0) {
            $this->assertFalse(
                $validator->validate(
                    $this->cleanPadding($validator, $message)
                )
            );
        }
    }

    /**
     * @dataProvider messageWithPaddingProvider
     *
     * @param string $message
     *
     * @return void
     */
    public function testWithoutPaddingValidation(string $message)
    {
        // Allow padding, disallow CRLF.
        $validator = $this->createValidator(false, false);
        $this->assertIdentifier($validator);

        $this->assertTrue(
            $validator->validate($message)
        );

        $this->assertTrue(
            $validator->validate(
                $this->cleanPadding($validator, $message)
            )
        );
    }

    /**
     * @dataProvider messageWithCRLFProvider
     *
     * @param string $message
     *
     * @return void
     */
    public function testWithCRLFValidation(string $message)
    {
        // Allow padding, allow CRLF.
        $validator = $this->createValidator(true, true);
        $this->assertIdentifier($validator);

        $this->assertTrue(
            $validator->validate($message)
        );

        if (!empty($message) && strlen($validator->getPaddingCharacter()) > 0) {
            $this->assertFalse(
                $validator->validate(
                    $this->cleanPadding($validator, $message)
                )
            );
        }
    }

    /**
     * Clean the padding from the given message, according to the given
     * validator.
     *
     * @param AbstractEncodingValidator $validator
     * @param string                             $message
     *
     * @return string
     */
    private function cleanPadding(
        AbstractEncodingValidator $validator,
        string $message
    ): string {
        return rtrim($message, $validator->getPaddingCharacter());
    }

    /**
     * @return void
     */
    public function testAlphabetSize()
    {
        $validator = $this->createValidator();
        $this->assertIdentifier($validator);

        $this->assertCount(
            $this->getAlphabetSize(),
            $validator->getAlphabet()
        );
    }
}
