<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\Tests\Integration;

use Checkvin\PaymentProviderSdk\Tests\TestCase;
use Checkvin\PaymentProviderSdk\Callbacks\LiqPayCallbackHandler;
use Checkvin\PaymentProviderSdk\Callbacks\WayForPayCallbackHandler;
use Checkvin\PaymentProviderSdk\Providers\LiqPay;
use Checkvin\PaymentProviderSdk\Clients\LiqPayClient;

class CallbackIntegrationTest extends TestCase
{
    private LiqPayCallbackHandler $liqPayHandler;
    private WayForPayCallbackHandler $wayForPayHandler;
    private string $liqPayPrivateKey = 'sandbox_private_key';
    private string $wayForPayPrivateKey = 'test_private_key';

    protected function setUp(): void
    {
        parent::setUp();
        $this->liqPayHandler = new LiqPayCallbackHandler($this->liqPayPrivateKey);
        $this->wayForPayHandler = new WayForPayCallbackHandler($this->wayForPayPrivateKey);
    }

    public function testLiqPayCallbackWithRealData(): void
    {
        // Arrange - Using the actual LiqPay callback data provided
        $data = 'eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIiwidmVyc2lvbiI6MywidHlwZSI6ImJ1eSIsInBheXR5cGUiOiJjYXJkIiwicHVibGljX2tleSI6InNhbmRib3hfaTc1ODEwODE4NzE5IiwiYWNxX2lkIjo0MTQ5NjMsIm9yZGVyX2lkIjoiMTc1MDEwNjIxNiIsImxpcXBheV9vcmRlcl9pZCI6IlpTUElWTzlNMTc1MDEwNjI2Mzc1OTkwMiIsImRlc2NyaXB0aW9uIjoiUGF5bWVudCBmb3IgaW52b2ljZSIsInNlbmRlcl9maXJzdF9uYW1lIjoiVGVzdCIsInNlbmRlcl9sYXN0X25hbWUiOiJUZXN0Iiwic2VuZGVyX2NhcmRfbWFzazIiOiI0MjQyNDIqNDIiLCJzZW5kZXJfY2FyZF9iYW5rIjoiVGVzdCIsInNlbmRlcl9jYXJkX3R5cGUiOiJ2aXNhIiwic2VuZGVyX2NhcmRfY291bnRyeSI6ODA0LCJpcCI6IjMuMTI0LjI2LjE0MyIsImFtb3VudCI6MTAuMCwiY3VycmVuY3kiOiJVU0QiLCJzZW5kZXJfY29tbWlzc2lvbiI6MC4wLCJyZWNlaXZlcl9jb21taXNzaW9uIjowLjE1LCJhZ2VudF9jb21taXNzaW9uIjowLjAsImFtb3VudF9kZWJpdCI6NDE2LjY3LCJhbW91bnRfY3JlZGl0Ijo0MTYuNjcsImNvbW1pc3Npb25fZGViaXQiOjAuMCwiY29tbWlzc2lvbl9jcmVkaXQiOjYuMjUsImN1cnJlbmN5X2RlYml0IjoiVUFIIiwiY3VycmVuY3lfY3JlZGl0IjoiVUFIIiwic2VuZGVyX2JvbnVzIjowLjAsImFtb3VudF9ib251cyI6MC4wLCJtcGlfZWNpIjoiNyIsImlzXzNkcyI6ZmFsc2UsImxhbmd1YWdlIjoidWsiLCJjcmVhdGVfZGF0ZSI6MTc1MDEwNjI2Mzc2MSwiZW5kX2RhdGUiOjE3NTAxMDYyNjM4OTUsInRyYW5zYWN0aW9uX2lkIjoyNjYyOTM0MTcxfQ==';
        $expectedSignature = base64_encode(sha1($this->liqPayPrivateKey . $data . $this->liqPayPrivateKey, true));
        
        $callbackData = [
            'signature' => $expectedSignature,
            'data' => $data
        ];

        // Act
        $result = $this->liqPayHandler->handle($callbackData);

        // Assert
        $this->assertInstanceOf(\Checkvin\PaymentProviderSdk\DTO\LiqPayCallbackData::class, $result);
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

    public function testWayForPayCallbackWithRealData(): void
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

        $result = $this->wayForPayHandler->handle($callbackData);

        $this->assertInstanceOf(\Checkvin\PaymentProviderSdk\DTO\WayForPayCallbackData::class, $result);
        $this->assertFalse($result->isSuccess());
    }

    public function testWayForPayCallbackWithApprovedTransaction(): void
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

        $result = $this->wayForPayHandler->handle($callbackData);

        $this->assertInstanceOf(\Checkvin\PaymentProviderSdk\DTO\WayForPayCallbackData::class, $result);
        $this->assertTrue($result->isSuccess());
        $this->assertEquals('ORDER-123', $result->getOrderId());
    }

    public function testLiqPayProviderVerifyCallbackWithRealData(): void
    {
        // Arrange
        $config = $this->createPaymentConfig(
            'sandbox_i75810818719',
            'sandbox_private_key',
            'test.com',
            'https://test.com/callback',
            'https://test.com/redirect'
        );
        
        $client = \Mockery::mock(LiqPayClient::class);
        $client->shouldReceive('config')->andReturn($config);
        
        $provider = new LiqPay($client);
        
        $callbackData = [
            'signature' => '5U27cHMYqUgZw2Y1MpG3q37tEbE=',
            'data' => 'eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIiwidmVyc2lvbiI6MywidHlwZSI6ImJ1eSIsInBheXR5cGUiOiJjYXJkIiwicHVibGljX2tleSI6InNhbmRib3hfaTc1ODEwODE4NzE5IiwiYWNxX2lkIjo0MTQ5NjMsIm9yZGVyX2lkIjoiMTc1MDEwNjIxNiIsImxpcXBheV9vcmRlcl9pZCI6IlpTUElWTzlNMTc1MDEwNjI2Mzc1OTkwMiIsImRlc2NyaXB0aW9uIjoiUGF5bWVudCBmb3IgaW52b2ljZSIsInNlbmRlcl9maXJzdF9uYW1lIjoiVGVzdCIsInNlbmRlcl9sYXN0X25hbWUiOiJUZXN0Iiwic2VuZGVyX2NhcmRfbWFzazIiOiI0MjQyNDIqNDIiLCJzZW5kZXJfY2FyZF9iYW5rIjoiVGVzdCIsInNlbmRlcl9jYXJkX3R5cGUiOiJ2aXNhIiwic2VuZGVyX2NhcmRfY291bnRyeSI6ODA0LCJpcCI6IjMuMTI0LjI2LjE0MyIsImFtb3VudCI6MTAuMCwiY3VycmVuY3kiOiJVU0QiLCJzZW5kZXJfY29tbWlzc2lvbiI6MC4wLCJyZWNlaXZlcl9jb21taXNzaW9uIjowLjE1LCJhZ2VudF9jb21taXNzaW9uIjowLjAsImFtb3VudF9kZWJpdCI6NDE2LjY3LCJhbW91bnRfY3JlZGl0Ijo0MTYuNjcsImNvbW1pc3Npb25fZGViaXQiOjAuMCwiY29tbWlzc2lvbl9jcmVkaXQiOjYuMjUsImN1cnJlbmN5X2RlYml0IjoiVUFIIiwiY3VycmVuY3lfY3JlZGl0IjoiVUFIIiwic2VuZGVyX2JvbnVzIjowLjAsImFtb3VudF9ib251cyI6MC4wLCJtcGlfZWNpIjoiNyIsImlzXzNkcyI6ZmFsc2UsImxhbmd1YWdlIjoidWsiLCJjcmVhdGVfZGF0ZSI6MTc1MDEwNjI2Mzc2MSwiZW5kX2RhdGUiOjE3NTAxMDYyNjM4OTUsInRyYW5zYWN0aW9uX2lkIjoyNjYyOTM0MTcxfQ=='
        ];

        // Mock the signature generation
        $client->shouldReceive('generateSignature')
            ->with($callbackData['data'])
            ->andReturn('5U27cHMYqUgZw2Y1MpG3q37tEbE=');

        // Act
        $result = $provider->verifyCallback($callbackData);

        // Assert
        $this->assertTrue($result);
    }

    private function generateWayForPaySignature(array $values): string
    {
        $data = implode(';', $values);
        return hash_hmac('md5', $data, 'test_private_key');
    }
} 