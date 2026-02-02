<?php

namespace Experteam\IndentificationNumberValidator;

use Experteam\IndentificationNumberValidator\I18n\Translator;
class ValidatorFactory
{
    public static function make(string $countryCode, ?string $acceptLanguage = null): CountryValidatorInterface
    {
        Translator::setLocale($acceptLanguage);

        return match (strtoupper($countryCode)) {
            'BR' => new Validators\BRValidator(),
            'GT' => new Validators\GTValidator(),
            default => throw new \Exception(
                Translator::trans('country_not_implemented', [
                    'countryCode' => $countryCode
                ])),
        };
    }
}
