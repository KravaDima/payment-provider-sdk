<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery;
use Checkvin\PaymentProviderSdk\Contracts\OrderInterface;
use Checkvin\PaymentProviderSdk\DTO\PaymentConfig;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockOrder(string $orderId = 'test-order-123', float $amount = 100.0, string $currency = 'USD', string $description = 'Test order'): OrderInterface
    {
        $order = Mockery::mock(OrderInterface::class);
        $order->shouldReceive('getOrderId')->andReturn($orderId);
        $order->shouldReceive('getAmount')->andReturn($amount);
        $order->shouldReceive('getCurrency')->andReturn($currency);
        $order->shouldReceive('getDescription')->andReturn($description);
        
        return $order;
    }

    protected function createPaymentConfig(
        string $publicKey = 'test_public_key',
        string $privateKey = 'test_private_key',
        string $merchantDomain = 'test.com',
        string $callbackUrl = 'https://test.com/callback',
        string $redirectUrl = 'https://test.com/redirect',
        bool $isDebug = false
    ): PaymentConfig {
        return new PaymentConfig(
            $publicKey,
            $privateKey,
            $merchantDomain,
            $callbackUrl,
            $redirectUrl,
            $isDebug
        );
    }
} 