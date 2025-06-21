<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Clients;

use App\Exceptions\Payment\LiqpayException;
use Checkvin\PaymentProviderSdk\DTO\PaymentConfig;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class LiqPayClient
{
    private const API_URL = 'api/3/checkout';

    public function __construct(
        public readonly PaymentConfig $config,
        public readonly ClientInterface $client
    ) {
    }

    public function generateSignature(string $data): string
    {
        return base64_encode(sha1($this->config->privateKey . $data . $this->config->privateKey, true));
    }

    /**
     * @throws GuzzleException
     * @throws PaymentException
     */
    public function request(array $payload): ResponseInterface
    {
        $data = $payload['data'] ?? '';
        $signature = $payload['signature'] ?? '';
        if (empty($data) || empty($signature)) {
            throw PaymentException::invalidSignature();
        }

        $response = $this->client->post(
            self::API_URL,
            [
                'form_params' => [
                    'data' => base64_encode(json_encode($data)),
                    'signature' => $signature,
                ]
            ],
        );

        if ($response->getStatusCode() !== 200) {
            throw PaymentException::requestError($response->getBody()->getContents());
        }

        return $response;
    }
}
