<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public static function requestError(string $message): self
    {
        return new self("Payment request error: {$message}");
    }

    public static function invalidSignature(): self
    {
        return new self('Invalid payment signature');
    }

    public static function invalidCallbackData(): self
    {
        return new self('Invalid callback data');
    }

    public static function paymentUrlNotReceived(): self
    {
        return new self('Payment URL was not received');
    }
} 