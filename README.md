# Introduction

The base encoding validators allow the user to validate base64, -32 and -16
encoded messages.
The validator will test if the string is encoded in the requested encoding.

Validators will validate against the specification of
[RFC 4648](https://tools.ietf.org/html/rfc4648).

Additionally, the [Base32 Crockford](http://www.crockford.com/wrmg/base32.html)
implementation is supported.

# Installation

```shell
composer require hylianshield/validator-base-encoding:^2.0
```
# Usage

The encoding validators can be configured to require padding or make it optional.
Additionally, some implementations require that partitioning will be supported.

[The RFC section on the interpretation of non-alphabet characters](https://tools.ietf.org/html/rfc4648#section-3.3)
states:

> Implementations MUST reject the encoded data if it contains
     characters outside the base alphabet when interpreting base-encoded
     data, unless the specification referring to this document explicitly
     states otherwise.  Such specifications may instead state, as MIME
     does, that characters outside the base encoding alphabet should
     simply be ignored when interpreting data ("be liberal in what you
     accept").  Note that this means that any adjacent carriage return/
     line feed (CRLF) characters constitute "non-alphabet characters" and
     are ignored.

Therefore, the constructors of the validators have the following signature:

```
bool $requirePadding = true,
bool $allowPartitioning = false
```

With the exception of the `Base16Validator`, which does not use padding and
therefore omits the first parameter, as well as the `Base32CrockfordValidator`,
which requires padding and as such omits it as well.

# Padding validation

```php
<?php
use HylianShield\Validator\BaseEncoding\Base64Validator;

// By default, padding is required.
$validator = new Base64Validator();
$validator->validate('d2Fycmlvcg=='); // true
$validator->validate('d2Fycmlvcg');   // false

// One can make padding validation optional.
$validator = new Base64Validator(false);
$validator->validate('d2Fycmlvcg=='); // true
$validator->validate('d2Fycmlvcg');   // true
```

# CRLF validation

```php
<?php
use HylianShield\Validator\BaseEncoding\Base64Validator;

// By default, partitioning is disallowed.
$validator = new Base64Validator();
$validator->validate('d2Fycmlvcg==');     // true
$validator->validate("d2Fycm\r\nlvcg=="); // false

// One can make partitioning allowed like so:
$validator = new Base64Validator(true, true);
$validator->validate('d2Fycmlvcg==');     // true
$validator->validate("d2Fycm\r\nlvcg=="); // true
```

# Supported encodings

Validators are defined in the `\HylianShield\Validator\BaseEncoding` namespace.

## Base 64 encoding

| Attribute     | Value                                                                 |
|:--------------|:----------------------------------------------------------------------|
| Name          | `base64`                                                              |
| Specification | [RFC 4648 - Section 4](https://tools.ietf.org/html/rfc4648#section-4) |
| Padding       | `=` (optional)                                                        |
| Partitioning  | `\r\n` (optional)                                                     |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base64Validator;

new Base64Validator(
    $requirePadding = true,
    $allowPartitions = false
);
```

## Base 64 Encoding with URL and Filename Safe Alphabet

| Attribute     | Value                                                                 |
|:--------------|:----------------------------------------------------------------------|
| Name          | `base64url`                                                           |
| Specification | [RFC 4648 - Section 5](https://tools.ietf.org/html/rfc4648#section-5) |
| Padding       | `=` (optional)                                                        |
| Partitioning  | `\r\n` (optional)                                                     |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base64UrlValidator;

new Base64UrlValidator(
    $requirePadding = true,
    $allowPartitions = false
);
```

## Base 32 encoding

| Attribute     | Value                                                                 |
|:--------------|:----------------------------------------------------------------------|
| Name          | `base32`                                                              |
| Specification | [RFC 4648 - Section 6](https://tools.ietf.org/html/rfc4648#section-6) |
| Padding       | `=` (optional)                                                        |
| Partitioning  | `\r\n` (optional)                                                     |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base32Validator;

new Base32Validator(
    $requirePadding = true,
    $allowPartitions = false
);
```

## Base 32 Encoding with Extended Hex Alphabet

| Attribute     | Value                                                                 |
|:--------------|:----------------------------------------------------------------------|
| Name          | `base32hex`                                                           |
| Specification | [RFC 4648 - Section 7](https://tools.ietf.org/html/rfc4648#section-7) |
| Padding       | `=` (optional)                                                        |
| Partitioning  | `\r\n` (optional)                                                     |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base32HexValidator;

new Base32HexValidator(
    $requirePadding = true,
    $allowPartitions = false
);
```

## Crockford's Base 32

| Attribute     | Value                                                                          |
|:--------------|:-------------------------------------------------------------------------------|
| Name          | `base32crockford`                                                              |
| Specification | [Crockford's Base 32 specification](http://www.crockford.com/wrmg/base32.html) |
| Padding       | `0` (required)                                                                 |
| Partitioning  | `-` (optional)                                                                 |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base32CrockfordValidator;

new Base32CrockfordValidator(
    $allowPartitions = true
);
```

## Base 16 encoding

| Attribute     | Value                                                                 |
|:--------------|:----------------------------------------------------------------------|
| Name          | `base16`                                                              |
| Specification | [RFC 4648 - Section 8](https://tools.ietf.org/html/rfc4648#section-8) |
| Padding       | No padding                                                            |
| Partitioning  | `\r\n` (optional)                                                     |

### Signature
```php
<?php
use HylianShield\Validator\BaseEncoding\Base16Validator;

new Base16Validator(
    $allowPartitions = false
);
```
