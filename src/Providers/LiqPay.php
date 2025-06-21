<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Providers;

use Checkvin\PaymentProviderSdk\Clients\LiqPayClient;
use Checkvin\PaymentProviderSdk\Contracts\OrderInterface;
use Checkvin\PaymentProviderSdk\Contracts\PaymentProviderInterface;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use JsonException;

final class LiqPay implements PaymentProviderInterface
{
    private const API_VERSION = '3';
    private const PROVIDER_NAME = 'liqpay';

    public function __construct(
        private readonly LiqPayClient $client
    ) {
    }

    public function getName(): string
    {
        return self::PROVIDER_NAME;
    }

    public function getAvailable(): bool
    {
        return true;
    }

    /**
     * @throws PaymentException
     */
    public function createInvoiceByOrder(OrderInterface $order): string
    {
        $config = $this->client->config;
        $data = [
            'public_key' => $config->publicKey,
            'version' => self::API_VERSION,
            'action' => 'pay',
            'amount' => $order->getAmount(),
            'currency' => $order->getCurrency(),
            'description' => $order->getDescription(),
            'order_id' => $order->getOrderId(),
            'result_url' => $config->redirectUrl,
            'server_url' => $config->callbackUrl,
        ];
        $signature = $this->client->generateSignature(base64_encode(json_encode($data)));

        try {
            $url = $this->createPayment(['data' => $data, 'signature' => $signature]);
        } catch (Throwable $e) {
            throw PaymentException::requestError($e->getMessage());
        }

        return $url;
    }

    /**
     * @throws PaymentException
     */
    public function createInvoiceByFreeAmount(float $amount, string $currency): string
    {
        $config = $this->client->config;
        $data = [
            'public_key' => $config->publicKey,
            'version' => self::API_VERSION,
            'action' => 'pay',
            'amount' => $amount,
            'currency' => $currency,
            'description' => 'Payment for invoice',
            'order_id' => (string) time(),
            'result_url' => $config->redirectUrl,
            'server_url' => $config->callbackUrl,
        ];

        $signature = $this->client->generateSignature(base64_encode(json_encode($data)));

        try {
            $url = $this->createPayment(['data' => $data, 'signature' => $signature]);
        } catch (Throwable $e) {
            throw PaymentException::requestError($e->getMessage());
        }

        return $url;
    }

    public function verifyCallback(array $data): bool
    {
        if (!isset($data['signature']) || !isset($data['data'])) {
            return false;
        }

        try {
            $decodedData = json_decode(base64_decode($data['data']), true, 512, JSON_THROW_ON_ERROR);
            if (!isset($decodedData['status']) || $decodedData['status'] !== 'success') {
                return false;
            }

            return $this->client->generateSignature($data['data']) === $data['signature'];
        } catch (JsonException) {
            return false;
        }
    }

    /**
     * @throws PaymentException
     */
    private function createPayment(array $order): string
    {
        if (!isset($order['data']) || !isset($order['signature'])) {
            throw PaymentException::invalidSignature();
        }

        $response = $this->client->request($order);

        if (!$response->hasHeader('X-Guzzle-Redirect-History')) {
            throw PaymentException::paymentUrlNotReceived();
        }

        $redirectArray = $response->getHeaders()['X-Guzzle-Redirect-History'];


        return array_shift($redirectArray);
    }
}
