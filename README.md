# Payment SDK

PHP SDK для інтеграції платіжних шлюзів LiqPay та WayForPay.

## Встановлення

```bash
composer require dmytro/payment-sdk
```

## Використання

### Конфігурація

```php
use Dmytro\PaymentSdk\DTO\PaymentConfig;
use Dmytro\PaymentSdk\Providers\LiqPay;
use Dmytro\PaymentSdk\Providers\WayForPay;
use Dmytro\PaymentSdk\Callbacks\LiqPayCallbackHandler;
use Dmytro\PaymentSdk\Callbacks\WayForPayCallbackHandler;
use GuzzleHttp\Client;

// Створення конфігурації
$config = new PaymentConfig(
    publicKey: 'your_public_key',
    privateKey: 'your_private_key',
    merchantDomain: 'https://your-domain.com',
    callbackUrl: '/payment/callback',
    redirectUrl: '/payment/redirect',
    isDebug: false
);

// Створення HTTP клієнта
$client = new Client();

// Ініціалізація платіжних провайдерів
$liqpay = new LiqPay($config, $client);
$wayforpay = new WayForPay($config, $client);

// Ініціалізація обробників колбеків
$liqpayCallbackHandler = new LiqPayCallbackHandler($config->getPrivateKey());
$wayforpayCallbackHandler = new WayForPayCallbackHandler($config->getPrivateKey());
```

### Створення платежу

```php
use Dmytro\PaymentSdk\Contracts\OrderInterface;

class Order implements OrderInterface
{
    public function __construct(
        private readonly string $id,
        private readonly float $amount,
        private readonly string $currency,
        private readonly string $description
    ) {
    }

    public function getOrderId(): string
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}

// Створення замовлення
$order = new Order(
    id: 'ORDER-123',
    amount: 100.00,
    currency: 'USD',
    description: 'Payment for order #123'
);

// Отримання URL для оплати
$paymentUrl = $liqpay->getPaymentUrl([
    'data' => $liqpay->getDataFromOrder($order),
    'signature' => $liqpay->getSignatureFromOrder($order)
]);
```

### Обробка колбеків

```php
// Обробка колбеку від LiqPay
$liqpayPayload = '{"signature":"6fHjF0NipSXNfnkHkY0PqbJogKg=","data":"eyJwYXltZW50X2lkIjoyNjYxNTA1MDMyLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIiwidmVyc2lvbiI6MywidHlwZSI6ImJ1eSIsInBheXR5cGUiOiJjYXJkIiwicHVibGljX2tleSI6ImkzMDkzOTYwNDU3IiwiYWNxX2lkIjo0MTQ5NjMsIm9yZGVyX2lkIjoiMTc0OTkxMDAzMyIsImxpcXBheV9vcmRlcl9pZCI6IlUyRkVJQUI2MTc0OTkxMDE1NjEzMzkyNCIsImRlc2NyaXB0aW9uIjoi0J/RgNC40L7QsdGA0LXRgtC10L3QuNC1INC/0LDQutC10YLQsCAtINCU0LvRjyDRgdC10LHRjysuINCa0L7Qu9C40YfQtdGB0YLQstC+INC/0YDQvtCy0LXRgNC+0LogLSA1LiDQodGC0L7QuNC80L7RgdGC0YwgLSAxMCBVU0QiLCJzZW5kZXJfZmlyc3RfbmFtZSI6IlBhd2XFgiBsYXMiLCJzZW5kZXJfbGFzdF9uYW1lIjoiUGF3ZcWCIGxhcyIsInNlbmRlcl9jYXJkX21hc2syIjoiNDc4MjAwKjI2Iiwic2VuZGVyX2NhcmRfYmFuayI6IkpQTU9SR0FOIENIQVNFIEJBTksgTi5BLiAtIERFQklUIiwic2VuZGVyX2NhcmRfdHlwZSI6InZpc2EiLCJzZW5kZXJfY2FyZF9jb3VudHJ5Ijo4NDAsImlwIjoiNzMuNDUuMTgxLjIiLCJhbW91bnQiOjEwLjAsImN1cnJlbmN5IjoiVVNEIiwic2VuZGVyX2NvbW1pc3Npb24iOjAuMCwicmVjZWl2ZXJfY29tbWlzc2lvbiI6MC4xNSwiYWdlbnRfY29tbWlzc2lvbiI6MC4wLCJhbW91bnRfZGViaXQiOjQxNi42NywiYW1vdW50X2NyZWRpdCI6NDE2LjY3LCJjb21taXNzaW9uX2RlYml0IjowLjAsImNvbW1pc3Npb25fY3JlZGl0Ijo2LjI1LCJjdXJyZW5jeV9kZWJpdCI6IlVBSCIsImN1cnJlbmN5X2NyZWRpdCI6IlVBSCIsInNlbmRlcl9ib251cyI6MC4wLCJhbW91bnRfYm9udXMiOjAuMCwiYXV0aGNvZGVfZGViaXQiOiIwODA5MTAiLCJycm5fZGViaXQiOiIwMDYwNjM2MTEwMzciLCJtcGlfZWNpIjoiNSIsImlzXzNkcyI6dHJ1ZSwibGFuZ3VhZ2UiOiJ1ayIsImNyZWF0ZV9kYXRlIjoxNzQ5OTEwMTU2MTM1LCJlbmRfZGF0ZSI6MTc0OTkxMDE1OTEzMSwidG9rZW4iOiIxNzQ5OTEwMTU2NDM2MTA5XzUwNzMwMjZfRU9YTFVkeHh4YXV2TjNweHFvM2Zhdk9UUTNSdElmbGFFVlVQTkw5TiIsImNvbmZpcm1fdG9rZW4iOiIxNzQ5OTEwMTU2NDM2MTA5XzUwNzMwMjZfRU9YTFVkeHh4YXV2TjNweHFvM2Zhdk9UUTNSdElmbGFFVlVQTkw5TiIsInRyYW5zYWN0aW9uX2lkIjoyNjYxNTA1MDMyLCJ0aWQiOiI0NjUxNjU1MDk1ODIzODkifQ=="}';

try {
    $result = $liqpayCallbackHandler->handle($liqpayPayload);
    // Обробка успішного платежу
    $orderId = $result['order_id'];
    $amount = $result['amount'];
    $currency = $result['currency'];
} catch (PaymentException $e) {
    // Обробка помилки
}

// Обробка колбеку від WayForPay
$wayforpayPayload = '{"merchantAccount":"test_merchant","orderReference":"ORDER-123","merchantSignature":"...","amount":100.00,"currency":"USD","authCode":"123456","cardPan":"41****8217","transactionStatus":"Approved","reasonCode":"1100"}';

try {
    $result = $wayforpayCallbackHandler->handle($wayforpayPayload);
    // Обробка успішного платежу
    $orderId = $result['orderReference'];
    $amount = $result['amount'];
    $currency = $result['currency'];
} catch (PaymentException $e) {
    // Обробка помилки
}
```

### Створення інвойсу з довільною сумою

```php
// Створення інвойсу на 100 USD
$paymentUrl = $liqpay->createFreeAmountInvoice(100.00, 'USD');
```

## Особливості

- Підтримка платіжних шлюзів LiqPay та WayForPay
- Типізовані інтерфейси та DTO
- Валідація колбеків
- Режим налагодження для тестування
- Створення інвойсів з довільною сумою
- Відповідність стандарту PSR-12
- Підтримка PHP 8.1+
- Повне покриття тестами

## Ліцензія

MIT 