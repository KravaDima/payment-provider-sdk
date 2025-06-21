<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Clients;

use Checkvin\PaymentProviderSdk\DTO\PaymentConfig;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class WayForPayClient
{
    private const API_URL = 'pay?behavior=offline';

    public function __construct(
        public readonly PaymentConfig $config,
        public readonly ClientInterface $client
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws PaymentException
     */
    public function request(array $dataWithSignature): ResponseInterface
    {
        if (! isset($dataWithSignature['merchantSignature'])) {
            throw PaymentException::invalidSignature();
        }

        $response = $this->client->post(
            self::API_URL,
            [
                'form_params' => $dataWithSignature
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw PaymentException::requestError($response->getBody()->getContents());
        }

        return $response;
    }
}
