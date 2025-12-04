# Identification Number Validator

A PHP library for validating identification numbers from multiple
countries.\
Provides a simple and unified interface for validating official national
documents.

## Install
    composer require experteam/indentification-number-validator
## Update
    composer update experteam/indentification-number-validator

## Usage

### Basic Example

``` php
use Experteam\IndentificationNumberValidator\ValidatorFactory;

$validator = ValidatorFactory::make('MX'); // Mexico example

$result = $validator->validate([
    'identificationNumber' => 'GODE561231GR8',
    'identificationCode'   => 'RFC'
]);
```

## Supported Countries

## Supported Countries

| Country | Code | Validator Class        |
|---------|------|------------------------|
| Mexico  | MX   | MXValidator     |

More countries can be added easily.


## Adding a New Country

1.  Create a new validator inside `src/Validators/`.

``` php
use Experteam\IndentificationNumberValidator\Contracts\IdentificationValidatorInterface;

class COValidator implements IdentificationValidatorInterface
{
    public function validate(string $number): bool
    {
        return true;
    }
}
```

2.  Register the validator in `ValidatorFactory.php`:

``` php
case 'CO':
    return new COValidator();
```

## Error Handling

``` php
use Experteam\IndentificationNumberValidator\Exceptions\UnsupportedCountryException;

try {
    ValidatorFactory::make('XX');
} catch (UnsupportedCountryException $e) {
    echo $e->getMessage();
}
```
