<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Callbacks;

use Checkvin\PaymentProviderSdk\Contracts\CallbackHandlerInterface;
use Checkvin\PaymentProviderSdk\DTO\WayForPayCallbackData;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use JsonException;

final class WayForPayCallbackHandler implements CallbackHandlerInterface
{
    public function __construct(
        private readonly string $privateKey
    ) {
    }

    /**
     * @throws PaymentException
     * @throws JsonException
     */
    public function handle(string|array $payload): WayForPayCallbackData
    {
        $data = json_decode($payload, true,512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON payload');
        }

        if (!isset($data['merchantSignature'])) {
            throw PaymentException::invalidCallbackData();
        }

        $signature = $data['merchantSignature'];
        unset($data['merchantSignature']);

        $expectedSignature = $this->generateSignature($data);

        if ($expectedSignature !== $signature) {
            throw PaymentException::invalidSignature();
        }

        return new WayForPayCallbackData($data);
    }

    public function supports(string $gateway): bool
    {
        return $gateway === 'wayforpay';
    }

    private function generateSignature(array $data): string
    {
        $string = implode(';', [
            $data['merchantAccount'],
            $data['orderReference'],
            $data['amount'],
            $data['currency'],
            $data['authCode'],
            $data['cardPan'],
            $data['transactionStatus'],
            $data['reasonCode']
        ]);

        return hash_hmac('md5', $string, $this->privateKey);
    }
}
