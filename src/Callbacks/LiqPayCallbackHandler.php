<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Callbacks;

use Checkvin\PaymentProviderSdk\Contracts\CallbackHandlerInterface;
use Checkvin\PaymentProviderSdk\DTO\LiqPayCallbackData;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use JsonException;

final class LiqPayCallbackHandler implements CallbackHandlerInterface
{
    public function __construct(
        private readonly string $privateKey
    ) {
    }

    /**
     * @throws PaymentException
     * @throws JsonException
     */
    public function handle(string|array $payload): LiqPayCallbackData
    {
        if (!isset($payload['signature']) || !$payload['data']) {
            throw PaymentException::invalidCallbackData();
        }

        $decodedData = json_decode(base64_decode($payload['data']), true, 512, JSON_THROW_ON_ERROR);
        $expectedSignature = base64_encode(sha1($this->privateKey . $payload['data'] . $this->privateKey, true));

        if ($expectedSignature !== $payload['signature']) {
            throw PaymentException::invalidCallbackData();
        }

        return new LiqPayCallbackData($decodedData);
    }

    public function supports(string $gateway): bool
    {
        return $gateway === 'liqpay';
    }
}
