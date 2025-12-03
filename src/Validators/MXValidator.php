<?php

namespace Experteam\IndentificationNumberValidator\Validators;

use Experteam\IndentificationNumberValidator\CountryValidatorInterface;

class MXValidator implements CountryValidatorInterface
{
    private const RFC_REGEX = '/^[A-Za-z&Ññ]{3,4}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Za-z0-9]{2}[0-9Aa]$/';

    public function validate(array $identification): array
    {
        if (
            !isset($identification['identificationNumber']) ||
            !isset($identification['identificationCode']) ||
            !isset($identification['identificationName'])
        ) {
            return [
                false,
                'The fields identificationNumber and identificationCode are required.'
            ];
        }

        $number = trim($identification['identificationNumber']);
        $code   = strtoupper(trim($identification['identificationCode']));
        $name   = strtoupper(trim($identification['identificationName']));

        return match ($code) {
            'RFC' => $this->validateRFC($number,$name),
            default => [
                false,
                'No validation configuration found for identificationCode: ' . $code
            ],
        };
    }

    private function validateRFC(string $rfc, string $name): array
    {
        $rfc = strtoupper(trim($rfc));

        if (preg_match(self::RFC_REGEX, $rfc)) {
            return [true, "{$name} is valid."];
        }

        return [false, "Invalid {$name} format."];
    }
}
