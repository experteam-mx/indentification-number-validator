<?php

namespace Experteam\IndentificationNumberValidator\Validators;
use Experteam\IndentificationNumberValidator\I18n\Translator;
use Experteam\IndentificationNumberValidator\CountryValidatorInterface;

class GTValidator implements CountryValidatorInterface
{
    public function validate(array $identification): array
    {
        if (
            !isset($identification['identificationNumber']) ||
            !isset($identification['identificationCode']) ||
            !isset($identification['identificationName'])
        ) {
            return [
                false,
                Translator::trans('required_fields')
            ];
        }

        $number = trim($identification['identificationNumber']);
        $code = strtoupper(trim($identification['identificationCode']));
        $name = strtoupper(trim($identification['identificationName']));

        return match ($code) {
            'CUI' => $this->validateCUI($number, $name),
            'NIT' => $this->validateNIT($number, $name),
            default => [
                false,
                Translator::trans('invalid_code', [
                    'code' => $code
                ])
            ],
        };
    }


    function validateCUI(string $number, string $name): array
    {
        $substring = substr($number, 0, 8);
        $checkDigit = intval(substr($number, 8, 1));

        $total = 0;
        $reversed = strrev($substring);

        for ($i = 0; $i < strlen($reversed); $i++) {
            $digit = intval($reversed[$i]);
            $total += $digit * ($i + 2);
        }

        $modulo = ($total * 10) % 11;

        if ($modulo === $checkDigit)
            return [
                true,
                Translator::trans('valid', [
                    'name' => $name
                ])
            ];
        else
            return [
                false,
                Translator::trans('invalid_format', [
                    'name' => $name
                ])
            ];
    }

    function validateNIT(string $number, string $name): array
    {
        $number = strtoupper(trim($number));
        if ($number === 'CF' || $number === 'C/F')
            return [true, "{$name} is valid."];


        $nit = $number;
        $pos = strpos($nit, '-');

        if ($pos === false) {
            $correlative = substr($number, 0, strlen($number) - 1);
            $digit = substr($number, -1);
            $nit = $correlative . '-' . $digit;
            $pos = strpos($nit, '-');
        }

        $correlative = substr($nit, 0, $pos);
        $checkDigit = strtoupper(substr($nit, $pos + 1));

        $factor = strlen($correlative) + 1;
        $digitSum = 0;

        for ($i = 0; $i < strlen($correlative); $i++) {
            $digit = intval(substr($correlative, $i, 1));
            $digitSum += $digit * $factor;
            $factor--;
        }

        $xMod11 = (11 - ($digitSum % 11)) % 11;

        $isValid =
            ($xMod11 === 10 && $checkDigit === 'K') ||
            ((string)$xMod11 === $checkDigit);

        if ($isValid) {
            return [
                true,
                Translator::trans('valid', [
                    'name' => $name
                ])
            ];
        }

        return [
            false,
            Translator::trans('invalid_format', [
                'name' => $name
            ])
        ];
    }

}
