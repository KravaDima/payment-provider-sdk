<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\DTO;

use Checkvin\PaymentProviderSdk\Tests\TestCase;
use Checkvin\PaymentProviderSdk\DTO\PaymentConfig;

class PaymentConfigTest extends TestCase
{
    public function testPaymentConfigCreation(): void
    {
        // Arrange
        $publicKey = 'test_public_key';
        $privateKey = 'test_private_key';
        $merchantDomain = 'test.com';
        $callbackUrl = 'https://test.com/callback';
        $redirectUrl = 'https://test.com/redirect';
        $isDebug = false;

        // Act
        $config = new PaymentConfig($publicKey, $privateKey, $merchantDomain, $callbackUrl, $redirectUrl, $isDebug);

        // Assert
        $this->assertEquals($publicKey, $config->publicKey);
        $this->assertEquals($privateKey, $config->privateKey);
        $this->assertEquals($merchantDomain, $config->merchantDomain);
        $this->assertEquals($callbackUrl, $config->callbackUrl);
        $this->assertEquals($redirectUrl, $config->redirectUrl);
        $this->assertEquals($isDebug, $config->isDebug);
    }

    public function testPaymentConfigWithDebugMode(): void
    {
        // Arrange
        $publicKey = 'sandbox_public_key';
        $privateKey = 'sandbox_private_key';
        $merchantDomain = 'sandbox.com';
        $callbackUrl = 'https://sandbox.com/callback';
        $redirectUrl = 'https://sandbox.com/redirect';
        $isDebug = true;

        // Act
        $config = new PaymentConfig($publicKey, $privateKey, $merchantDomain, $callbackUrl, $redirectUrl, $isDebug);

        // Assert
        $this->assertEquals($publicKey, $config->publicKey);
        $this->assertEquals($privateKey, $config->privateKey);
        $this->assertEquals($merchantDomain, $config->merchantDomain);
        $this->assertEquals($callbackUrl, $config->callbackUrl);
        $this->assertEquals($redirectUrl, $config->redirectUrl);
        $this->assertTrue($config->isDebug);
    }

    public function testPaymentConfigDefaultDebugValue(): void
    {
        // Arrange
        $publicKey = 'test_public_key';
        $privateKey = 'test_private_key';
        $merchantDomain = 'test.com';
        $callbackUrl = 'https://test.com/callback';
        $redirectUrl = 'https://test.com/redirect';

        // Act
        $config = new PaymentConfig($publicKey, $privateKey, $merchantDomain, $callbackUrl, $redirectUrl);

        // Assert
        $this->assertFalse($config->isDebug);
    }

    public function testPaymentConfigPropertiesAreReadonly(): void
    {
        // Arrange
        $config = new PaymentConfig('test', 'test', 'test.com', 'https://test.com/callback', 'https://test.com/redirect');

        // Assert
        $this->assertTrue(property_exists($config, 'publicKey'));
        $this->assertTrue(property_exists($config, 'privateKey'));
        $this->assertTrue(property_exists($config, 'merchantDomain'));
        $this->assertTrue(property_exists($config, 'callbackUrl'));
        $this->assertTrue(property_exists($config, 'redirectUrl'));
        $this->assertTrue(property_exists($config, 'isDebug'));
        
        // Verify properties are readonly (can't be modified after construction)
        $reflection = new \ReflectionClass($config);
        $properties = ['publicKey', 'privateKey', 'merchantDomain', 'callbackUrl', 'redirectUrl', 'isDebug'];
        
        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isReadOnly());
        }
    }
} 