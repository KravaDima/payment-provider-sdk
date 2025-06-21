<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Contracts;

interface PaymentProviderInterface
{
    public function getName(): string;
    public function getAvailable(): bool;
    public function createInvoiceByOrder(OrderInterface $order): string;
    public function createInvoiceByFreeAmount(float $amount, string $currency): string;
    public function verifyCallback(array $data): bool;
} 