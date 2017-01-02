<?php
namespace HylianShield\Tests\Validator\BaseEncoding;

use HylianShield\Validator\BaseEncoding\Base64UrlValidator;

class Base64UrlValidatorTest extends Base64ValidatorTest
{
    /**
     * @return string
     */
    protected function getValidatorClassName(): string
    {
        return Base64UrlValidator::class;
    }
}
