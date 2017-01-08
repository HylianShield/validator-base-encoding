<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\Base64Validator;

class Base64ValidatorTest extends BaseEncodingValidatorTestCase
{
    /**
     * @return string
     */
    protected function getValidatorClassName(): string
    {
        return Base64Validator::class;
    }

    /**
     * @return string[][]
     */
    public function messageWithPaddingProvider(): array
    {
        return [
            [''],
            ['d2Fycmlvcg==']
        ];
    }

    /**
     * @return string[][]
     */
    public function messageWithPartitionProvider(): array
    {
        return [
            [''],
            ["d2Fycm\r\nlvcg=="]
        ];
    }

    /**
     * @return int
     */
    protected function getAlphabetSize(): int
    {
        return 64;
    }
}
