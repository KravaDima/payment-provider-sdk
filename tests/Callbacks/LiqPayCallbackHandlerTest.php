<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\Callbacks;

use Checkvin\PaymentProviderSdk\Tests\TestCase;
use Checkvin\PaymentProviderSdk\Callbacks\LiqPayCallbackHandler;
use Checkvin\PaymentProviderSdk\DTO\LiqPayCallbackData;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;

class LiqPayCallbackHandlerTest extends TestCase
{
    private LiqPayCallbackHandler $handler;
    private string $privateKey = 'sandbox_private_key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new LiqPayCallbackHandler($this->privateKey);
    }

    public function testSupportsLiqPay(): void
    {
        $this->assertTrue($this->handler->supports('liqpay'));
    }

    public function testSupportsOtherGateway(): void
    {
        $this->assertFalse($this->handler->supports('wayforpay'));
    }

    public function testHandleWithValidCallbackData(): void
    {
        // Arrange
        $data = 'eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIiwidmVyc2lvbiI6MywidHlwZSI6ImJ1eSIsInBheXR5cGUiOiJjYXJkIiwicHVibGljX2tleSI6InNhbmRib3hfaTc1ODEwODE4NzE5IiwiYWNxX2lkIjo0MTQ5NjMsIm9yZGVyX2lkIjoiMTc1MDEwNjIxNiIsImxpcXBheV9vcmRlcl9pZCI6IlpTUElWTzlNMTc1MDEwNjI2Mzc1OTkwMiIsImRlc2NyaXB0aW9uIjoiUGF5bWVudCBmb3IgaW52b2ljZSIsInNlbmRlcl9maXJzdF9uYW1lIjoiVGVzdCIsInNlbmRlcl9sYXN0X25hbWUiOiJUZXN0Iiwic2VuZGVyX2NhcmRfbWFzazIiOiI0MjQyNDIqNDIiLCJzZW5kZXJfY2FyZF9iYW5rIjoiVGVzdCIsInNlbmRlcl9jYXJkX3R5cGUiOiJ2aXNhIiwic2VuZGVyX2NhcmRfY291bnRyeSI6ODA0LCJpcCI6IjMuMTI0LjI2LjE0MyIsImFtb3VudCI6MTAuMCwiY3VycmVuY3kiOiJVU0QiLCJzZW5kZXJfY29tbWlzc2lvbiI6MC4wLCJyZWNlaXZlcl9jb21taXNzaW9uIjowLjE1LCJhZ2VudF9jb21taXNzaW9uIjowLjAsImFtb3VudF9kZWJpdCI6NDE2LjY3LCJhbW91bnRfY3JlZGl0Ijo0MTYuNjcsImNvbW1pc3Npb25fZGViaXQiOjAuMCwiY29tbWlzc2lvbl9jcmVkaXQiOjYuMjUsImN1cnJlbmN5X2RlYml0IjoiVUFIIiwiY3VycmVuY3lfY3JlZGl0IjoiVUFIIiwic2VuZGVyX2JvbnVzIjowLjAsImFtb3VudF9ib251cyI6MC4wLCJtcGlfZWNpIjoiNyIsImlzXzNkcyI6ZmFsc2UsImxhbmd1YWdlIjoidWsiLCJjcmVhdGVfZGF0ZSI6MTc1MDEwNjI2Mzc2MSwiZW5kX2RhdGUiOjE3NTAxMDYyNjM4OTUsInRyYW5zYWN0aW9uX2lkIjoyNjYyOTM0MTcxfQ==';
        $expectedSignature = base64_encode(sha1($this->privateKey . $data . $this->privateKey, true));
        
        $callbackData = [
            'signature' => $expectedSignature,
            'data' => $data
        ];

        // Act
        $result = $this->handler->handle($callbackData);

        // Assert
        $this->assertInstanceOf(LiqPayCallbackData::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('1750106216', $result->getOrderId());
        $this->assertEquals('10', $result->getAmount());
        $this->assertEquals('success', $result->getTransactionStatus());
        $this->assertEquals(2662934171, $result->getPaymentId());
        $this->assertEquals('424242*42', $result->getSenderCardMask());
        $this->assertEquals('Test', $result->getSenderCardBank());
        $this->assertEquals('visa', $result->getSenderCardType());
        $this->assertEquals(804, $result->getSenderCardCountry());
        $this->assertEquals('3.124.26.143', $result->getIp());
        $this->assertEquals('Payment for invoice', $result->getDescription());
        $this->assertEquals('pay', $result->getAction());
        $this->assertEquals('card', $result->getPaymentType());
        $this->assertEquals('uk', $result->getLanguage());
        $this->assertEquals(1750106263761, $result->getCreateDate());
        $this->assertEquals(1750106263895, $result->getEndDate());
    }

    public function testHandleWithMissingSignature(): void
    {
        // Arrange
        $callbackData = [
            'data' => 'eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIn0='
        ];

        // Act & Assert
        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid callback data');
        
        $this->handler->handle($callbackData);
    }

    public function testHandleWithMissingData(): void
    {
        // Arrange
        $callbackData = [
            'signature' => 'test_signature'
        ];

        // Act & Assert
        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid callback data');
        
        $this->handler->handle($callbackData);
    }

    public function testHandleWithInvalidSignature(): void
    {
        // Arrange
        $callbackData = [
            'signature' => 'invalid_signature',
            'data' => 'eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIn0='
        ];

        // Act & Assert
        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid callback data');
        
        $this->handler->handle($callbackData);
    }

    public function testHandleWithInvalidBase64Data(): void
    {
        // Arrange
        $callbackData = [
            'signature' => 'test_signature',
            'data' => 'invalid_base64_data'
        ];

        // Act & Assert
        $this->expectException(\JsonException::class);
        
        $this->handler->handle($callbackData);
    }

    public function testHandleWithInvalidJsonData(): void
    {
        // Arrange
        $callbackData = [
            'signature' => 'test_signature',
            'data' => base64_encode('invalid json data')
        ];

        // Act & Assert
        $this->expectException(\JsonException::class);
        
        $this->handler->handle($callbackData);
    }
} 