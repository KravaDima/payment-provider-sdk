<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\DTO;

use Checkvin\PaymentProviderSdk\Tests\TestCase;
use Checkvin\PaymentProviderSdk\DTO\PaymentData;

class PaymentDataTest extends TestCase
{
    public function testPaymentDataCreation(): void
    {
        // Arrange
        $provider = 'liqpay';
        $paymentUrl = 'https://www.liqpay.ua/checkout/card?data=test&signature=test';

        // Act
        $paymentData = new PaymentData($provider, $paymentUrl);

        // Assert
        $this->assertEquals($provider, $paymentData->provider);
        $this->assertEquals($paymentUrl, $paymentData->paymentUrl);
    }

    public function testPaymentDataWithDifferentProvider(): void
    {
        // Arrange
        $provider = 'wayforpay';
        $paymentUrl = 'https://secure.wayforpay.com/pay?token=test';

        // Act
        $paymentData = new PaymentData($provider, $paymentUrl);

        // Assert
        $this->assertEquals($provider, $paymentData->provider);
        $this->assertEquals($paymentUrl, $paymentData->paymentUrl);
    }

    public function testPaymentDataPropertiesAreReadonly(): void
    {
        // Arrange
        $paymentData = new PaymentData('test', 'https://test.com');

        // Assert
        $this->assertTrue(property_exists($paymentData, 'provider'));
        $this->assertTrue(property_exists($paymentData, 'paymentUrl'));
        
        // Verify properties are readonly (can't be modified after construction)
        $reflection = new \ReflectionClass($paymentData);
        $providerProperty = $reflection->getProperty('provider');
        $paymentUrlProperty = $reflection->getProperty('paymentUrl');
        
        $this->assertTrue($providerProperty->isReadOnly());
        $this->assertTrue($paymentUrlProperty->isReadOnly());
    }
} 