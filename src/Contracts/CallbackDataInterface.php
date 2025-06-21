<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Contracts;

interface CallbackDataInterface
{
    public function isSuccess(): bool;
    public function getOrderId(): string;
    public function getAmount(): string;
    public function getTransactionStatus(): string;
} 