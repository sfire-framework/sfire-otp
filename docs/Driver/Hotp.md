# HOTP Driver

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

sFire HOTP Driver is a HMAC-based One-time Password generator based on *[hash-based message authentication codes](https://en.wikipedia.org/wiki/Hash-based_message_authentication_code)* (HMAC)



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
use sFire\Otp\Driver\Hotp;
```



### Instance

```php
$hotp = new Hotp();
```



### Configuration

Below are the default values that the package uses.

#### Default settings

- Default [digits](#setting-digits) is 6
- Default [algorithm](#setting-algorithm) is sha1



#### Setting a secret

Each Hotp instance needs to have a secret. This secret may be a string of any length and character:

```php
$hotp -> setSecret('ABCDEFGIJK0123456789');
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
$hotp -> setDigits(int $digits): self
```

###### Example

```php
$hotp -> setDigits(10); //Will generate a 10 digits OTP
```



##### Setting algorithm

You may set a different hash algorithm by using the `setAlgorithm()` method.

###### Syntax

```php
$hotp -> setAlgorithm(string $algorithm): self
```

###### Example

```php
$hotp -> setAlgorithm('ripemd160');
```



## Usage

#### Generating OTP

To generate an OTP (one time password), you may use the `counter()`method. 

##### Syntax

```php
$hotp -> counter(int $count): string
```

##### Example: Generating OTP

```php
$hotp -> counter(1); //Output similar to: 045712
```



#### Verifying OTP

To verify a given OTP, you may use the `verify()` method. The second parameter will be the counter which the OTP was generated.

##### Syntax

```php
$hotp -> verify(string $otp, int $counter): bool 
```

##### Example 1: Validating OTP

```php
$hotp -> verify('749252', 1);
```



#### Generating OTP secret

You can generate a secret by using the `generateSecret()` method. This method will return a 16 (by default) characters long string to use to validate OTP's. Because not all numbers are allowed (0, 1, 8 and 9) within a secret, sFire made it easy to generate a valid secret. This method will also set the key once generated.

##### Syntax

```php
$hotp -> generateSecret(int $length = 16, bool $numbers = true, bool $letters = true, bool $capitals = true): string
```

##### Example 1: Generate a secret

```php
$hotp -> generateSecret();
```

Will result in something similar to:

```
string(16) "XH4qbHmv25N4y4Mn"
```

##### Example 2: Generate a secret with only lowercase letters and a length of 30

```php
$hotp -> generateSecret(30, false, true, false);
```

Will result in something similar to:

```
string(30) "gxflgovbjtbzfjclryrwtbjoyhfaot"
```



#### Retrieving OTP secret

To retrieve a generated or custom set OTP secret, you may use the `getSecret()` method.

##### Syntax

```php
$hotp -> getSecret(): ?string
```

##### Example

```php
$hotp -> generateSecret();
$hotp -> getSecret();
```

Will result in something similar to:

```
string(16) "XH4qbHmv25N4y4Mn"
```



#### Generating provisioning URL

A provisioning URL can be used to save the account and secret in an authenticator app. This URL may be generated with the `getProvisioningUrl()` method. This method will `urlencode` a given name and counter to be URL safe.

##### Syntax

```php
$hotp -> getProvisioningUrl(string $name, int $initialCount): string
```

##### Example

```php
$hotp -> getProvisioningUrl('Brenda', 52);
//Similar to: "otpauth://hotp/Brenda?secret=B72eFIg52Q3kLaE6&counter=52"
```



## Examples

### Creating provisioning url

```php
$hotp = new Otp();
$hotp -> generateSecret();
$url = $hotp -> getProvisioningUrl('Brenda', 52);
$secret = $hotp -> getSecret();

var_dump($url, $secret);
```

Will result in:

```text
string(45) "otpauth://hotp/Brenda?secret=B72eFIg52Q3kLaE6&counter=52"
string(16) "B72eFIg52Q3kLaE6"
```



### Verifying user's OTP

In the example below, the user has submitted the OTP via a form. The system will check if the given OTP is valid against the saved user secret.

```php
if(isset($_POST['otp'])) {

    $userSecret = 'ABCDEFGHIJK';
    $userCounter = 25;

    $hotp = new Hotp();
    $hotp -> setSecret($userSecret);

    if($hotp -> verify($_POST['otp'], $userCounter)) {
      //OTP is valid
    }
}
```



## Notes

### Chaining

Some of the provided methods may be chained together:

```php
$hotp -> setSecret('ABCDEFGHIJK') -> verify('541028', 25);
```



### OTP is always a string

A valid OTP is always of the type `string` and not of the type `int` because an OTP may have a leading `0`. If this value is cast to an `int` the leading `0` will be removed and will result in an invalid OTP value.

```php
var_dump((int) 054725); //Output: "54725"
```