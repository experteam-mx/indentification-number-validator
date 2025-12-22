<?php

namespace Experteam\IndentificationNumberValidator\Validators;

use Experteam\IndentificationNumberValidator\I18n\Translator;
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
            Translator::trans('invalid_code', [
                'code' => $code
            ])
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

        if ($response["status"] === "success" && $response['data']['identification']['valid'] === false) {
            return [false, Translator::trans('invalid_format', [
                'name' => $name
            ])];
        }

        return [
            true,
            Translator::trans('valid', [
                'name' => $name
            ])
        ];
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
                return [
                    false,
                    Translator::trans('required_field', [
                        'field' => $field
                    ])
                ];
            }
        }

        if (
            !isset($identification['parameters']) ||
            !is_array($identification['parameters'])
        ) {
            return [
                false,
                Translator::trans('required_parameters')
            ];
        }

        $paramRequired = ['apiUrl', 'token'];

        foreach ($paramRequired as $param) {
            if (
                !isset($identification['parameters'][$param]) ||
                empty($identification['parameters'][$param])
            ) {
                return [
                    false,
                    Translator::trans('required_param', [
                        'param' => $param
                    ])
                ];
            }
        }

        return [true, ""];
    }

}
