<?php

namespace Experteam\IndentificationNumberValidator\Validators;

use Experteam\IndentificationNumberValidator\Services\HttpService;
use Experteam\IndentificationNumberValidator\CountryValidatorInterface;


class BRValidator implements CountryValidatorInterface
{

    public function validate(array $identification): array
    {
        [$status, $msg] = $this->validateRequiredFields($identification);

        if (!$status) {
            return [$status, $msg];
        }

        $number = trim($identification['identificationNumber']);
        $code = trim($identification['identificationCode']);
        $name = trim($identification['identificationName']);
        $baseUrl = trim($identification['parameters']['apiUrl']);
        $authToken = trim($identification['parameters']['token']);
        $serviceCodes = ['CNPJ', 'CPF'];

        if (in_array($code, $serviceCodes, true)) {
            return $this->validateService($number, $name, $baseUrl, $authToken);
        }

        return [
            false,
            "No validation configuration found for identificationCode: {$code}"
        ];
    }

    private function validateService(string $number, string $name, string $baseUrl, string $authToken): array
    {
        $service = new HttpService(
            baseUrl: $baseUrl,
            authToken: $authToken
        );

        $parameters = [
            "identification_number" => $number,
            "validate_state_tax" => false
        ];

        $response = $service->post("/identifications/br-validation", $parameters);
        if ($response["status"] === "fail")
            return [false, $response["data"]["message"]];

        return [true, "{$name} is valid."];
    }


    private function validateRequiredFields(array $identification): array
    {
        $required = [
            'identificationNumber',
            'identificationCode',
            'identificationName'
        ];

        foreach ($required as $field) {
            if (!isset($identification[$field]) || $identification[$field] === '') {
                return [false, "The field {$field} is required."];
            }
        }

        if (
            !isset($identification['parameters']) ||
            !is_array($identification['parameters'])
        ) {
            return [false, "The field parameters is required and must be an array."];
        }

        $paramRequired = ['apiUrl', 'token'];

        foreach ($paramRequired as $param) {
            if (
                !isset($identification['parameters'][$param]) ||
                empty($identification['parameters'][$param])
            ) {
                return [false, "The parameter {$param} is required."];
            }
        }

        return [true, ""];
    }

}
