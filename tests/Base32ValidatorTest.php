<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\Base32Validator;

class Base32ValidatorTest extends BaseEncodingValidatorTestCase
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
        return Base32Validator::class;
    }

    /**
     * @return string[][]
     */
    public function messageWithPaddingProvider(): array
    {
        return [
            [''],
            ['JB4WY2LBNZJWQ2LFNRSA====']
        ];
    }

    /**
     * @return string[][]
     */
    public function messageWithPartitionProvider(): array
    {
        return [
            [''],
            ["JB4WY2LBNZJ\r\nWQ2LFNRSA===="]
        ];
    }
}
