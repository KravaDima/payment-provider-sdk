<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests;

use Checkvin\PaymentProviderSdk\PaymentManager;
use Checkvin\PaymentProviderSdk\Contracts\PaymentProviderInterface;
use Checkvin\PaymentProviderSdk\DTO\PaymentData;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use Mockery;

class PaymentManagerTest extends TestCase
{
    public function testCreatePaymentDataWithAvailableProviders(): void
    {
        // Arrange
        $order = $this->createMockOrder();
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(true);
        $provider1->shouldReceive('getName')->andReturn('liqpay');
        $provider1->shouldReceive('createInvoiceByOrder')->with($order)->andReturn('https://liqpay.com/pay/123');
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(true);
        $provider2->shouldReceive('getName')->andReturn('wayforpay');
        $provider2->shouldReceive('createInvoiceByOrder')->with($order)->andReturn('https://wayforpay.com/pay/456');
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createPaymentData($order);
        
        // Assert
        $this->assertCount(2, $result);
        $this->assertInstanceOf(PaymentData::class, $result->get(0));
        $this->assertEquals('liqpay', $result->get(0)->provider);
        $this->assertEquals('https://liqpay.com/pay/123', $result->get(0)->paymentUrl);
        $this->assertEquals('wayforpay', $result->get(1)->provider);
        $this->assertEquals('https://wayforpay.com/pay/456', $result->get(1)->paymentUrl);
    }
    
    public function testCreatePaymentDataWithUnavailableProviders(): void
    {
        // Arrange
        $order = $this->createMockOrder();
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(false);
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(false);
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createPaymentData($order);
        
        // Assert
        $this->assertCount(0, $result);
    }
    
    public function testCreatePaymentDataWithMixedAvailability(): void
    {
        // Arrange
        $order = $this->createMockOrder();
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(false);
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(true);
        $provider2->shouldReceive('getName')->andReturn('wayforpay');
        $provider2->shouldReceive('createInvoiceByOrder')->with($order)->andReturn('https://wayforpay.com/pay/456');
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createPaymentData($order);
        
        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('wayforpay', $result->get(0)->provider);
        $this->assertEquals('https://wayforpay.com/pay/456', $result->get(0)->paymentUrl);
    }
    
    public function testCreatePaymentDataWithProviderException(): void
    {
        // Arrange
        $order = $this->createMockOrder();
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(true);
        $provider1->shouldReceive('createInvoiceByOrder')->with($order)->andThrow(new PaymentException('Test error'));
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(true);
        $provider2->shouldReceive('getName')->andReturn('wayforpay');
        $provider2->shouldReceive('createInvoiceByOrder')->with($order)->andReturn('https://wayforpay.com/pay/456');
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createPaymentData($order);
        
        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('wayforpay', $result->get(0)->provider);
        $this->assertEquals('https://wayforpay.com/pay/456', $result->get(0)->paymentUrl);
    }
    
    public function testCreateFreeAmountInvoiceWithAvailableProviders(): void
    {
        // Arrange
        $amount = 150.0;
        $currency = 'EUR';
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(true);
        $provider1->shouldReceive('getName')->andReturn('liqpay');
        $provider1->shouldReceive('createInvoiceByFreeAmount')->with($amount, $currency)->andReturn('https://liqpay.com/pay/789');
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(true);
        $provider2->shouldReceive('getName')->andReturn('wayforpay');
        $provider2->shouldReceive('createInvoiceByFreeAmount')->with($amount, $currency)->andReturn('https://wayforpay.com/pay/012');
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createFreeAmountInvoice($amount, $currency);
        
        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('liqpay', $result->get(0)->provider);
        $this->assertEquals('https://liqpay.com/pay/789', $result->get(0)->paymentUrl);
        $this->assertEquals('wayforpay', $result->get(1)->provider);
        $this->assertEquals('https://wayforpay.com/pay/012', $result->get(1)->paymentUrl);
    }
    
    public function testCreateFreeAmountInvoiceWithProviderException(): void
    {
        // Arrange
        $amount = 150.0;
        $currency = 'EUR';
        
        $provider1 = Mockery::mock(PaymentProviderInterface::class);
        $provider1->shouldReceive('getAvailable')->andReturn(true);
        $provider1->shouldReceive('createInvoiceByFreeAmount')->with($amount, $currency)->andThrow(new \Exception('Test error'));
        
        $provider2 = Mockery::mock(PaymentProviderInterface::class);
        $provider2->shouldReceive('getAvailable')->andReturn(true);
        $provider2->shouldReceive('getName')->andReturn('wayforpay');
        $provider2->shouldReceive('createInvoiceByFreeAmount')->with($amount, $currency)->andReturn('https://wayforpay.com/pay/012');
        
        $paymentManager = new PaymentManager([$provider1, $provider2]);
        
        // Act
        $result = $paymentManager->createFreeAmountInvoice($amount, $currency);
        
        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('wayforpay', $result->get(0)->provider);
        $this->assertEquals('https://wayforpay.com/pay/012', $result->get(0)->paymentUrl);
    }
} 