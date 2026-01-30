<?php

namespace Experteam\IndentificationNumberValidator\Services;

class HttpService
{
    private string $baseUrl;
    private ?string $authToken;
    private string $authType;

    public function __construct(string $baseUrl, ?string $authToken = null, string $authType = 'Bearer')
    {
        $this->baseUrl  = rtrim($baseUrl, '/');
        $this->authToken = $authToken;
        $this->authType  = $authType;
    }

    public function post(string $endpoint, array $data): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];

        if ($this->authToken) {
            $headers[] = "Authorization: {$this->authType} {$this->authToken}";
        }

        $jsonBody = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonBody,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);
        $curlError = curl_error($ch);
        $httpCode = $info['http_code'] ?? 0;
        curl_close($ch);
        $response = json_decode($response, true);

        if(is_null($response))
            throw new \Exception('Api Service: System critical error' );

        if ($response['status'] === 'error')
            throw new \Exception('Api Service:' .$response['message']);

        return $response;
    }
}
