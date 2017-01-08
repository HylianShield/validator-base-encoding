<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\AbstractEncodingValidator;
use HylianShield\Validator\BaseEncoding\Base16Validator;

class Base16ValidatorTest extends BaseEncodingValidatorTestCase
{
    /**
     * @return int
     */
    protected function getAlphabetSize(): int
    {
        return 16;
    }

    /**
     * @return string
     */
    protected function getValidatorClassName(): string
    {
        return Base16Validator::class;
    }

    /**
     * @return string[][]
     */
    public function messageWithPaddingProvider(): array
    {
        return [
            [''],
            // Base 16 has no padding in its implementation.
            ['6162']
        ];
    }

    /**
     * @return string[][]
     */
    public function messageWithPartitionProvider(): array
    {
        return [
            [''],
            ["61\r\n62"]
        ];
    }

    /**
     * @param bool $requirePadding
     * @param bool $allowCRLF
     *
     * @return AbstractEncodingValidator
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function createValidator(
        bool $requirePadding = true,
        bool $allowCRLF = false
    ): AbstractEncodingValidator {
        /** @var AbstractEncodingValidator $validator */
        $validator = $this->getReflection()->newInstance($allowCRLF);

        return $validator;
    }
}
