<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\Exceptions;

use Checkvin\PaymentProviderSdk\Tests\TestCase;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;

class PaymentExceptionTest extends TestCase
{
    public function testRequestErrorException(): void
    {
        // Arrange
        $message = 'Network error occurred';

        // Act
        $exception = PaymentException::requestError($message);

        // Assert
        $this->assertInstanceOf(PaymentException::class, $exception);
        $this->assertEquals("Payment request error: {$message}", $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testInvalidSignatureException(): void
    {
        // Act
        $exception = PaymentException::invalidSignature();

        // Assert
        $this->assertInstanceOf(PaymentException::class, $exception);
        $this->assertEquals('Invalid payment signature', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testInvalidCallbackDataException(): void
    {
        // Act
        $exception = PaymentException::invalidCallbackData();

        // Assert
        $this->assertInstanceOf(PaymentException::class, $exception);
        $this->assertEquals('Invalid callback data', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testPaymentUrlNotReceivedException(): void
    {
        // Act
        $exception = PaymentException::paymentUrlNotReceived();

        // Assert
        $this->assertInstanceOf(PaymentException::class, $exception);
        $this->assertEquals('Payment URL was not received', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionInheritance(): void
    {
        // Act
        $exception = PaymentException::requestError('Test error');

        // Assert
        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionWithPreviousException(): void
    {
        // Arrange
        $previousException = new \Exception('Previous error');
        $message = 'Current error';

        // Act
        $exception = new PaymentException($message, 0, $previousException);

        // Assert
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($previousException, $exception->getPrevious());
    }
} 