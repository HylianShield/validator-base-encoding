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
composer require hylianshield/validator-base-encoding:^1.0
```
# Supported encodings

Validators are defined in the `\HylianShield\Validator\BaseEncoding` namespace.

| Validator            | Reference                                                               | Padding | Partitioning |
|:---------------------|:------------------------------------------------------------------------|:--------|:-------------|
| `Base64Validator`    | [RFC 4648 - Section 4](https://tools.ietf.org/html/rfc4648#section-4)   | `=`     | `\r\n`       |
| `Base64UrlValidator` | [RFC 4648 - Section 5](https://tools.ietf.org/html/rfc4648#section-5)   | `=`     | `\r\n`       |
| `Base32Validator`    | [RFC 4648 - Section 6](https://tools.ietf.org/html/rfc4648#section-6)   | `=`     | `\r\n`       |
| `Base32HexValidator` | [RFC 4648 - Section 7](https://tools.ietf.org/html/rfc4648#section-7)   | `=`     | `\r\n`       |
| `Base32Crockford`    | [Douglas Crockford - Base32](http://www.crockford.com/wrmg/base32.html) | `0`     | `-`          |
| `Base16Validator`    | [RFC 4648 - Section 8](https://tools.ietf.org/html/rfc4648#section-8)   |         | `\r\n`       |

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
therefore omits the first parameter.

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
