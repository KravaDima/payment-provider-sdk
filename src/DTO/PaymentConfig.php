<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\DTO;

class PaymentConfig
{
    public function __construct(
        public readonly string $publicKey,
        public readonly string $privateKey,
        public readonly string $merchantDomain,
        public readonly string $callbackUrl,
        public readonly string $redirectUrl,
        public readonly bool $isDebug = false
    ) {
    }
} 