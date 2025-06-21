<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Contracts;

interface CallbackHandlerInterface
{
    public function handle(string $payload): CallbackDataInterface;
    public function supports(string $gateway): bool;
} 