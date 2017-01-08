<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\AbstractEncodingValidator;
use HylianShield\Validator\BaseEncoding\DefinitionInterface;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

abstract class BaseEncodingValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /** @var string */
    private $identifierPattern = '/^base(64|32|16)(\w+)?\((require-)?padding(-optional)?,(no-)?partitioning\)$/';

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
    abstract public function messageWithPartitionProvider(): array;

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
     * @param bool $allowPartitioning
     *
     * @return AbstractEncodingValidator
     */
    protected function createValidator(
        bool $requirePadding = true,
        bool $allowPartitioning = false
    ): AbstractEncodingValidator {
        /** @var AbstractEncodingValidator $validator */
        $validator = $this->getReflection()->newInstance(
            $requirePadding,
            $allowPartitioning
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
     * Clean the padding from the given message, according to the given
     * definition.
     *
     * @param DefinitionInterface $definition
     * @param string              $message
     *
     * @return string
     */
    protected function cleanPadding(
        DefinitionInterface $definition,
        string $message
    ): string {
        return rtrim($message, $definition->getPaddingCharacter());
    }

    /**
     * Remove the partitioning symbols from the given message, according to
     * the given definition.
     *
     * @param DefinitionInterface $definition
     * @param string              $message
     *
     * @return string
     */
    protected function cleanPartitioning(
        DefinitionInterface $definition,
        string $message
    ): string {
        return str_replace(
            $definition->getPartitionSeparator(),
            '',
            $message
        );
    }

    /**
     * Normalize the given message according to the given definition.
     *
     * @param DefinitionInterface $definition
     * @param string              $message
     *
     * @return string
     */
    protected function normalizeMessage(
        DefinitionInterface $definition,
        string $message
    ): string {
        return $this->cleanPadding(
            $definition,
            $this->cleanPartitioning($definition, $message)
        );
    }

    /**
     * @return void
     */
    public function testWithNonString()
    {
        $validator = $this->createValidator();
        $this->assertFalse($validator->validate(12));
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
        // Require padding, disallow partitioning.
        $validator = $this->createValidator(true, false);
        $this->assertIdentifier($validator);

        $this->assertTrue(
            $validator->validate($message)
        );

        $normalized = $this->normalizeMessage($validator, $message);

        if (strlen($normalized) > 0
            && strlen($validator->getPaddingCharacter()) > 0
        ) {
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
        // Allow padding, disallow partitioning.
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
     * @dataProvider messageWithPartitionProvider
     *
     * @param string $message
     *
     * @return void
     */
    public function testWithPartitionValidation(string $message)
    {
        // Allow padding, allow partitioning.
        $validator = $this->createValidator(true, true);
        $this->assertIdentifier($validator);

        $this->assertTrue(
            $validator->validate($message)
        );

        $normalized = $this->normalizeMessage($validator, $message);

        if (strlen($normalized) > 0
            && strlen($validator->getPaddingCharacter()) > 0
        ) {
            $this->assertFalse(
                $validator->validate(
                    $this->cleanPadding($validator, $message)
                )
            );
        }
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
