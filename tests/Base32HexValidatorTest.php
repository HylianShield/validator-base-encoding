<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\Base32HexValidator;

class Base32HexValidatorTest extends Base32ValidatorTest
{
    /**
     * @return string
     */
    protected function getValidatorClassName(): string
    {
        return Base32HexValidator::class;
    }

    /**
     * @return string[][]
     */
    public function messageWithPaddingProvider(): array
    {
        return [
            [''],
            ['0ABRACADABRA====']
        ];
    }

    /**
     * @return string[][]
     */
    public function messageWithPartitionProvider(): array
    {
        return [
            [''],
            ["0ABRA\r\nCADABRA===="]
        ];
    }
}
