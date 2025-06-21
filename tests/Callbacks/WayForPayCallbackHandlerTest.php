<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\Callbacks;

use Checkvin\PaymentProviderSdk\Callbacks\WayForPayCallbackHandler;
use Checkvin\PaymentProviderSdk\DTO\WayForPayCallbackData;
use Checkvin\PaymentProviderSdk\Exceptions\PaymentException;
use Checkvin\PaymentProviderSdk\Tests\TestCase;

class WayForPayCallbackHandlerTest extends TestCase
{
    private WayForPayCallbackHandler $handler;
    private string $privateKey = 'test_private_key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = new WayForPayCallbackHandler($this->privateKey);
    }

    public function testHandleWithValidApprovedCallbackData(): void
    {
        $callbackData = json_encode([
            'merchantAccount' => 'test_merchant',
            'orderReference' => 'ORDER-123',
            'amount' => 100.50,
            'currency' => 'USD',
            'authCode' => '123456',
            'cardPan' => '4444555566667777',
            'transactionStatus' => 'Approved',
            'reasonCode' => '1100',
            'merchantSignature' => $this->generateWayForPaySignature([
                'test_merchant',
                'ORDER-123',
                100.50,
                'USD',
                '123456',
                '4444555566667777',
                'Approved',
                '1100'
            ])
        ]);

        $result = $this->handler->handle($callbackData);

        $this->assertInstanceOf(WayForPayCallbackData::class, $result);
        $this->assertEquals('ORDER-123', $result->getOrderId());
        $this->assertEquals('100.5', $result->getAmount());
        $this->assertEquals('USD', $result->getCurrency());
        $this->assertTrue($result->isSuccess());
    }

    public function testHandleWithDeclinedTransaction(): void
    {
        $callbackData = json_encode([
            'merchantAccount' => 'test_merchant',
            'orderReference' => 'ORDER-123',
            'amount' => 100.50,
            'currency' => 'USD',
            'authCode' => '123456',
            'cardPan' => '4444555566667777',
            'transactionStatus' => 'Declined',
            'reasonCode' => '1105',
            'merchantSignature' => $this->generateWayForPaySignature([
                'test_merchant',
                'ORDER-123',
                100.50,
                'USD',
                '123456',
                '4444555566667777',
                'Declined',
                '1105'
            ])
        ]);

        $result = $this->handler->handle($callbackData);

        $this->assertInstanceOf(WayForPayCallbackData::class, $result);
        $this->assertFalse($result->isSuccess());
    }

    public function testHandleWithInvalidSignature(): void
    {
        $callbackData = json_encode([
            'merchantAccount' => 'test_merchant',
            'orderReference' => 'ORDER-123',
            'amount' => 100.50,
            'currency' => 'USD',
            'authCode' => '123456',
            'cardPan' => '4444555566667777',
            'transactionStatus' => 'Approved',
            'reasonCode' => '1100',
            'merchantSignature' => 'invalid_signature'
        ]);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid payment signature');

        $this->handler->handle($callbackData);
    }

    public function testHandleWithMissingRequiredFields(): void
    {
        $callbackData = json_encode([
            'merchantAccount' => 'test_merchant',
            'orderReference' => 'ORDER-123'
        ]);

        $this->expectException(PaymentException::class);
        $this->expectExceptionMessage('Invalid callback data');

        $this->handler->handle($callbackData);
    }

    public function testHandleWithInvalidJson(): void
    {
        $callbackData = '{"invalid_json"}';

        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage('Syntax error');

        $this->handler->handle($callbackData);
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->handler->supports('wayforpay'));
        $this->assertFalse($this->handler->supports('liqpay'));
    }

    private function generateWayForPaySignature(array $values): string
    {
        $data = implode(';', $values);
        return hash_hmac('md5', $data, $this->privateKey);
    }
} 