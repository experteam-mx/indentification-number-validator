<?php

namespace Experteam\IndentificationNumberValidator;

class ValidatorFactory
{
    public static function make(string $countryCode): CountryValidatorInterface
    {
        return match (strtoupper($countryCode)) {
            'BR' => new Validators\BRValidator(),
            'GT' => new Validators\GTValidator(),
            default => throw new \Exception("Country validator not implemented: $countryCode"),
        };
    }
}
