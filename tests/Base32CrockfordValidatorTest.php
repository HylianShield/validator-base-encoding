<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\Base32CrockfordValidator;
use HylianShield\Validator\BaseEncoding\DefinitionInterface;

class Base32CrockfordValidatorTest extends BaseEncodingValidatorTestCase
{
    /**
     * @return int
     */
    protected function getAlphabetSize(): int
    {
        return 32;
    }

    /**
     * @return string
     */
    protected function getValidatorClassName(): string
    {
        return Base32CrockfordValidator::class;
    }

    /**
     * @return string[][]
     */
    public function messageWithPaddingProvider(): array
    {
        return [
            // Empty.
            [''],
            ['00000000'],
            ['00000000*'],
            // Padding within base alphabet.
            ['0000123456789ABCDEFGHJKMNPQRSTVWA'],
            // Variations on extended alphabet.
            ['0000123456789ABCDEFGHJKMNPQRSTVW'],
            ['0000123456789ABCDEFGHJKMNPQRSTVW*'],
            ['0000123456789ABCDEFGHJKMNPQRSTVW~'],
            ['0000123456789ABCDEFGHJKMNPQRSTVW$'],
            ['0000123456789ABCDEFGHJKMNPQRSTVW='],
            ['0000123456789ABCDEFGHJKMNPQRSTVWU'],
            ['0000123456789ABCDEFGHJKMNPQRSTVWu'],
            // Variations on mis-pronunciations of characters.
            ['000o123456789ABCDEFGHJKMNPQRSTVW*'],
            ['000O123456789ABCDEFGHJKMNPQRSTVW*'],
            ['0000i23456789ABCDEFGHJKMNPQRSTVW*'],
            ['0000I23456789ABCDEFGHJKMNPQRSTVW*'],
            ['0000l23456789ABCDEFGHJKMNPQRSTVW*'],
            ['0000L23456789ABCDEFGHJKMNPQRSTVW*']
        ];
    }

    /**
     * @return string[][]
     */
    public function messageWithPartitionProvider(): array
    {
        return [
            // Empty.
            [''],
            // Variations on check symbol.
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y'],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y*'],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y~'],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y$'],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y='],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-YU'],
            ['0012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Yu']
        ];
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
        $checkSymbol = '';

        $pattern = sprintf('/^((.{%d})*)(.?)$/', $definition->getGroupSize());

        if (preg_match($pattern, $message, $matches)) {
            $message     = next($matches);
            $checkSymbol = end($matches);
        }

        return parent::cleanPadding($definition, $message) . $checkSymbol;
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
        static $pattern;

        if ($pattern === null) {
            $alphabet = array_merge(
                iterator_to_array($definition->getAlphabet()),
                Base32CrockfordValidator::EXTENDED_ALPHABET
            );

            $pattern = sprintf(
                '/^(([%s]{%d})*)([%s]?)$/',
                preg_quote(
                    implode('', array_slice($alphabet, 0, 32)),
                    '/'
                ),
                $definition->getGroupSize(),
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

        // Uppercase the alphabet.
        $message = strtoupper($message);

        // Replace mis-pronunciations.
        $message = str_replace(
            ['O', 'I', 'L'],
            ['0', '1', '1'],
            $message
        );

        return parent::normalizeMessage($definition, $message);
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
    }
}
