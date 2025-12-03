<?php

namespace Experteam\IndentificationNumberValidator;

class ValidatorFactory
{
    public static function make(string $countryCode): CountryValidatorInterface
    {
        return match (strtoupper($countryCode)) {
            'MX' => new Validators\MXValidator(),
            default => throw new \Exception("Country validator not implemented: $countryCode"),
        };
    }
}
