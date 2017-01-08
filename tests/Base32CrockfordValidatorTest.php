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
            //[''],
            ['00000000'],
            ['00000000*'],
            // Variations on check symbol.
            ['0123456789ABCDEFGHJKMNPQRSTVW000'],
            ['0123456789ABCDEFGHJKMNPQRSTVW000*'],
            ['0123456789ABCDEFGHJKMNPQRSTVW000~'],
            ['0123456789ABCDEFGHJKMNPQRSTVW000$'],
            ['0123456789ABCDEFGHJKMNPQRSTVW000='],
            ['0123456789ABCDEFGHJKMNPQRSTVW000U'],
            ['0123456789ABCDEFGHJKMNPQRSTVW000u'],
            // Variations on mis-pronunciations of characters.
            ['o123456789ABCDEFGHJKMNPQRSTVW000*'],
            ['O123456789ABCDEFGHJKMNPQRSTVW000*'],
            ['0i23456789ABCDEFGHJKMNPQRSTVW000*'],
            ['0I23456789ABCDEFGHJKMNPQRSTVW000*'],
            ['0l23456789ABCDEFGHJKMNPQRSTVW000*'],
            ['0L23456789ABCDEFGHJKMNPQRSTVW000*']
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
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0'],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0*'],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0~'],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0$'],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0='],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0U'],
            ['012345-6789AB-CDEFGH-JKMNPQ-RSTVWX-Y0u']
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

        if (preg_match('/^(.*?)([\*\~\$\=Uu]?)$/', $message, $matches)) {
            $message     = next($matches);
            $checkSymbol = next($matches);
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
        // Remove the check symbol.
        $message = preg_replace(
            '/[\*\~\$\=Uu]?$/',
            '',
            $message
        );

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
}
