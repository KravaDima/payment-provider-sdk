<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Providers;

use Checkvin\PaymentProviderSdk\Clients\WayForPayClient;
use Checkvin\PaymentProviderSdk\Contracts\OrderInterface;
use Checkvin\PaymentProviderSdk\Contracts\PaymentProviderInterface;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

final class WayForPay implements PaymentProviderInterface
{
    private const PROVIDER_NAME = 'wayforpay';

    public function __construct(
        public readonly WayForPayClient $client
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
            'merchantAccount' => $config->publicKey,
            'merchantDomainName' => $config->merchantDomain,
            'orderReference' => $order->getOrderId(),
            'orderDate' => time(),
            'amount' => $order->getAmount(),
            'currency' => $order->getCurrency(),
            'productName[]' => $order->getDescription(),
            'productCount[]' => 1,
            'productPrice[]' => $order->getAmount(),
        ];

        return $this->createPayment($data);
    }

    /**
     * @throws PaymentException
     */
    public function createInvoiceByFreeAmount(float $amount, string $currency): string
    {
        $config = $this->client->config;
        $data = [
            'merchantAccount' => $config->publicKey,
            'merchantDomainName' => $config->merchantDomain,
            'orderReference' => uniqid('INV-', true),
            'orderDate' => time(),
            'amount' => $amount,
            'currency' => $currency,
            'productName[]' => 'Payment for invoice',
            'productCount[]' => 1,
            'productPrice[]' => $amount,
        ];

        return $this->createPayment($data);
    }

    public function verifyCallback(array $data): bool
    {
        if (!isset($data['merchantSignature'])) {
            return false;
        }

        try {
            $signature = $this->createSignature(implode(';', array_values($data)));
            return hash_equals($signature, $data['merchantSignature']);
        } catch (\Throwable) {
            return false;
        }
    }

    private function createSignature(string $data): string
    {
        return hash_hmac('md5', $data, $this->client->config->privateKey);
    }

    /**
     * @throws PaymentException
     */
    private function createPayment(array $data): string
    {
        $signature = $this->createSignature(implode(';', array_values($data)));
        $dataWithSignature = array_merge($data, [
            'merchantSignature' => $signature,
            'merchantTransactionSecureType' => 'AUTO',
            'returnUrl' => $this->client->config->redirectUrl,
            'serviceUrl' => $this->client->config->callbackUrl
        ]);

        $response = $this->client->request($dataWithSignature);
        $resultArray = json_decode($response->getBody()->getContents(), true);

        if (empty($resultArray) || empty($resultArray['url'])) {
            throw PaymentException::paymentUrlNotReceived();
        }

        return $resultArray['url'];
    }
}
