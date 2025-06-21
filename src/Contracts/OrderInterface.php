<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Contracts;

interface OrderInterface
{
    public function getOrderId(): string;
    public function getAmount(): string;
    public function getCurrency(): string;
    public function getDescription(): string;
}
