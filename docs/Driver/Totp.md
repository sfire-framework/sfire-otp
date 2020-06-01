# TOTP Driver

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)

  [Setup](#setup)

  - [Namespace](#namespace)
  - [Instance](#instance)
  - [Configuration](#configuration)
- [Usage](#usage)
  - [Generating OTP](#generating-otp)
  - [Verifying OTP](#verifying-otp)
  - [Generating OTP secret](#generating-otp-secret)
  - [Retrieving OTP secret](#retrieving-otp-secret)
  - [Generating provisioning URL](#generating-provisioning-url)
- [Examples](#examples)
  - [Creating provisioning URL](#creating-provisioning-url)
  - [Verifying user's OTP](#verifying-users-otp)
- [Notes](#notes)
  - [Chaining](#chaining)
  - [OTP is always a string](#otp-is-always-a-string)



## Introduction

sFire TOTP Driver is a Time-based one time password generator and verifier that is an extension of the HMAC-based One-time Password algorithm (HOTP).



## Requirements

There are no requirements for this package.



## Installation

Install this package using [Composer](https://getcomposer.org/):
```shell script
composer require sfire-framework/sfire-otp
```



## Setup

### Namespace
```php
use sFire\Otp\Driver\Totp;
```



### Instance

```php
$totp = new Totp();
```



### Configuration

Below are the default values that the package uses.

#### Default settings

- Default [digits](#setting-digits) is 6
- Default [algorithm](#setting-algorithm) is sha1
- Default [interval](#setting-interval) is 30 seconds



#### Setting a secret

Each Totp instance needs to have a secret. This secret may be a string of any length and character:
```php
$totp -> setSecret('ABCDEFGIJK0123456789');
```

You may retrieve the secret with the `getSecret()` method:

```php
$hotp -> getSecret(); //Output "ABCDEFGIJK0123456789"
```



#### Overwriting settings

##### Setting digits
A generated OTP will have 6 digits by default. To overwrite this, you may use the `setDigits()` method.
###### Syntax
```php
$totp -> setDigits(int $digits): self
```
###### Example
```php
$totp -> setDigits(10); //Will generate a 10 digits OTP
```



##### Setting algorithm

You may set a different hash algorithm by using the `setAlgorithm()` method.
###### Syntax
```php
$totp -> setAlgorithm(string $algorithm): self
```
###### Example
```php
$totp -> setAlgorithm('ripemd160');
```



##### Setting interval

By default the generated OTP is 30 seconds valid. You may change this setting by using the `setInterval()` method.
###### Syntax
```php
$totp -> setInterval(int $interval): self
```
###### Example
```php
$totp -> setInterval(60);
```



## Usage

#### Generating OTP
To generate an OTP (one time password), you may use the `now()` or `timestamp()` methods. The `timestamp()` method uses a given unix timestamp/epoch value to generate the OTP, while the `now()` method is a shortcut to the `timestamp()` method with the current unix timestamp/epoch.

##### Syntax
```php
$totp -> timestamp(int $timestamp): string
$totp -> now(): string
```

##### Example 1: Generating OTP with timestamp method
```php
$totp -> timestamp(time() + 120); //Output similar to: 045712
```

##### Example 2: Generating OTP with now method
```php
$totp -> now(); //Output similar to: 541875
```



#### Verifying OTP

To verify a given OTP, you may use the `verify()` method. The default timestamp is the current timestamp/epoch, but may be set manual.

##### Syntax

```php
$totp -> verify(string $totp, ?int $discrepancy = 0, int $timestamp = null): bool
```

##### Example 1: Validating OTP

```php
$totp -> verify('749252');
```

##### Example 2: Validating OTP with discrepancy

Discrepancy is the factor of interval allowed on either side of the given timestamp. For example, if a code with an interval of 30 seconds is generated at 10:00:00, a discrepancy of 1 will allow a period of 30 seconds on either side of the interval resulting in a valid code from 09:59:30 to 10:00:29.

```php
$totp -> verify('749252', 1);
```

##### Example 3: Validating OTP with a given timestamp

You may also set a custom timestamp to validate the OTP against.

```php
$totp -> verify('749252', null, time() + 30);
```



#### Generating OTP secret

You can generate a secret by using the `generateSecret()` method. This method will return a 16 (by default) characters long string to use to validate OTP's. Because not all numbers are allowed (0, 1, 8 and 9) within a secret, sFire made it easy to generate a valid secret. This method will also set the key once generated.

##### Syntax

```php
$totp -> generateSecret(int $length = 16, bool $numbers = true, bool $letters = true, bool $capitals = true): string
```

##### Example 1: Generate a secret

```php
$totp -> generateSecret();
```

Will result in something similar to:

```
string(16) "XH4qbHmv25N4y4Mn"
```

##### Example 2: Generate a secret with only lowercase letters and a length of 30

```php
$totp -> generateSecret(30, false, true, false);
```

Will result in something similar to:

```
string(30) "gxflgovbjtbzfjclryrwtbjoyhfaot"
```



#### Retrieving OTP secret

To retrieve a generated or custom set OTP secret, you may use the `getSecret()` method.

##### Syntax

```php
$totp -> getSecret(): ?string
```

##### Example

```php
$totp -> generateSecret();
$totp -> getSecret();
```

Will result in something similar to:

```
string(16) "XH4qbHmv25N4y4Mn"
```



#### Generating provisioning URL

A provisioning URL can be used to save the account and secret in an authenticator app. This URL may be generated with the `getProvisioningUrl()` method. This method will `urlencode` a given name to be URL safe.

##### Syntax

```php
$totp -> getProvisioningUrl(string $name): string
```

##### Example

```php
$totp -> getProvisioningUrl('Brenda');
//Similar to: "otpauth://totp/Brenda?secret=B72eFIg52Q3kLaE6"
```



## Examples

### Creating provisioning url

```php
$totp = new Otp();
$totp -> generateSecret();
$url = $totp -> getProvisioningUrl('Brenda');
$secret = $totp -> getSecret();

var_dump($url, $secret);
```

Will result in:

```text
string(45) "otpauth://totp/Brenda?secret=B72eFIg52Q3kLaE6"
string(16) "B72eFIg52Q3kLaE6"
```



### Verifying user's OTP

In the example below, the user has submitted the OTP via a form. The system will check if the given OTP is valid against the saved user secret.

```php
if(isset($_POST['otp'])) {

    $userSecret = 'ABCDEFGHIJK';

    $totp = new Totp();
    $totp -> setSecret($userSecret);

    if($totp -> verify($_POST['otp'])) {
    	//OTP is valid
    }
}
```



## Notes

### Chaining
Some of the provided methods may be chained together:
```php
$totp -> setSecret('ABCDEFGHIJK') -> verify('541028');
```



### OTP is always a string

A valid OTP is always of the type `string` and not of the type `int` because an OTP may have a leading `0`. If this value is cast to an `int` the leading `0` will be removed and will result in an invalid OTP value.

```php
var_dump((int) 054725); //Output: "54725"
```