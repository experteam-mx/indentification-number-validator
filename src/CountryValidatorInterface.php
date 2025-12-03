<?php

namespace Experteam\IndentificationNumberValidator;

interface CountryValidatorInterface
{
    public function validate(array $identification): array;

}
