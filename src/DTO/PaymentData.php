<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\DTO;

class PaymentData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $paymentUrl
    ) {
    }
} 