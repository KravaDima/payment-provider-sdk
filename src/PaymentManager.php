<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk;

use Checkvin\PaymentProviderSdk\Contracts\OrderInterface;
use Checkvin\PaymentProviderSdk\Contracts\PaymentProviderInterface;
use Checkvin\PaymentProviderSdk\DTO\PaymentData;
use Illuminate\Support\Collection;

class PaymentManager
{
    /**
     * @param array<PaymentProviderInterface> $providers
     */
    public function __construct(
        private readonly array $providers
    ) {
    }

    public function createPaymentData(OrderInterface $order): Collection
    {
        $result = new Collection();

        foreach ($this->providers as $provider) {
            if (! $provider->getAvailable()) {
                continue;
            }

            try {
                $result->add(new PaymentData(
                    $provider->getName(),
                    $provider->createInvoiceByOrder($order)
                ));
            } catch (\Throwable $e) {
            }
        }

        return $result;
    }

    public function createFreeAmountInvoice(float $amount, string $currency): Collection
    {
        $result = new Collection();

        foreach ($this->providers as $provider) {
            if (! $provider->getAvailable()) {
                continue;
            }

            try {
                $paymentUrl = $provider->createInvoiceByFreeAmount($amount, $currency);
                $result->add(new PaymentData($provider->getName(), $paymentUrl));
            } catch (\Throwable $e) {
            }
        }

        return $result;
    }
}
