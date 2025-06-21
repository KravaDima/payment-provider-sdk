# Payment Provider SDK Tests

This directory contains comprehensive tests for the Payment Provider SDK package, covering both LiqPay and WayForPay payment gateways.

## Test Structure

```
tests/
├── TestCase.php                           # Base test case with common utilities
├── PaymentManagerTest.php                 # Tests for PaymentManager class
├── Providers/
│   ├── LiqPayTest.php                     # Tests for LiqPay provider
│   └── WayForPayTest.php                  # Tests for WayForPay provider
├── Callbacks/
│   ├── LiqPayCallbackHandlerTest.php      # Tests for LiqPay callback handler
│   └── WayForPayCallbackHandlerTest.php   # Tests for WayForPay callback handler
├── DTO/
│   ├── PaymentDataTest.php                # Tests for PaymentData DTO
│   └── PaymentConfigTest.php              # Tests for PaymentConfig DTO
├── Exceptions/
│   └── PaymentExceptionTest.php           # Tests for PaymentException
└── Integration/
    └── CallbackIntegrationTest.php        # Integration tests with real callback data
```

## Test Coverage

### Unit Tests

1. **PaymentManagerTest** - Tests the main payment manager functionality:
   - Creating payment data with available providers
   - Handling unavailable providers
   - Error handling when providers throw exceptions
   - Creating free amount invoices

2. **Provider Tests** - Tests for individual payment providers:
   - **LiqPayTest**: Tests LiqPay provider functionality
   - **WayForPayTest**: Tests WayForPay provider functionality
   - Payment creation with orders and free amounts
   - Callback verification
   - Error handling

3. **Callback Handler Tests** - Tests for callback processing:
   - **LiqPayCallbackHandlerTest**: Tests LiqPay callback processing
   - **WayForPayCallbackHandlerTest**: Tests WayForPay callback processing
   - Signature verification
   - Data validation
   - Error handling for invalid data

4. **DTO Tests** - Tests for data transfer objects:
   - **PaymentDataTest**: Tests PaymentData DTO
   - **PaymentConfigTest**: Tests PaymentConfig DTO
   - Property validation and readonly checks

5. **Exception Tests** - Tests for custom exceptions:
   - **PaymentExceptionTest**: Tests PaymentException class
   - Different exception types and messages

### Integration Tests

**CallbackIntegrationTest** - Tests using real callback data:
- Tests with actual LiqPay callback data provided
- Tests with actual WayForPay callback data provided
- Verifies that the SDK correctly processes real-world callback scenarios
- Tests both successful and declined transactions

## Real Callback Data Used

### LiqPay Callback
```json
{
  "signature": "5U27cHMYqUgZw2Y1MpG3q37tEbE=",
  "data": "eyJwYXltZW50X2lkIjoyNjYyOTM0MTcxLCJhY3Rpb24iOiJwYXkiLCJzdGF0dXMiOiJzdWNjZXNzIiwidmVyc2lvbiI6MywidHlwZSI6ImJ1eSIsInBheXR5cGUiOiJjYXJkIiwicHVibGljX2tleSI6InNhbmRib3hfaTc1ODEwODE4NzE5IiwiYWNxX2lkIjo0MTQ5NjMsIm9yZGVyX2lkIjoiMTc1MDEwNjIxNiIsImxpcXBheV9vcmRlcl9pZCI6IlpTUElWTzlNMTc1MDEwNjI2Mzc1OTkwMiIsImRlc2NyaXB0aW9uIjoiUGF5bWVudCBmb3IgaW52b2ljZSIsInNlbmRlcl9maXJzdF9uYW1lIjoiVGVzdCIsInNlbmRlcl9sYXN0X25hbWUiOiJUZXN0Iiwic2VuZGVyX2NhcmRfbWFzazIiOiI0MjQyNDIqNDIiLCJzZW5kZXJfY2FyZF9iYW5rIjoiVGVzdCIsInNlbmRlcl9jYXJkX3R5cGUiOiJ2aXNhIiwic2VuZGVyX2NhcmRfY291bnRyeSI6ODA0LCJpcCI6IjMuMTI0LjI2LjE0MyIsImFtb3VudCI6MTAuMCwiY3VycmVuY3kiOiJVU0QiLCJzZW5kZXJfY29tbWlzc2lvbiI6MC4wLCJyZWNlaXZlcl9jb21taXNzaW9uIjowLjE1LCJhZ2VudF9jb21taXNzaW9uIjowLjAsImFtb3VudF9kZWJpdCI6NDE2LjY3LCJhbW91bnRfY3JlZGl0Ijo0MTYuNjcsImNvbW1pc3Npb25fZGViaXQiOjAuMCwiY29tbWlzc2lvbl9jcmVkaXQiOjYuMjUsImN1cnJlbmN5X2RlYml0IjoiVUFIIiwiY3VycmVuY3lfY3JlZGl0IjoiVUFIIiwic2VuZGVyX2JvbnVzIjowLjAsImFtb3VudF9ib251cyI6MC4wLCJtcGlfZWNpIjoiNyIsImlzXzNkcyI6ZmFsc2UsImxhbmd1YWdlIjoidWsiLCJjcmVhdGVfZGF0ZSI6MTc1MDEwNjI2Mzc2MSwiZW5kX2RhdGUiOjE3NTAxMDYyNjM4OTUsInRyYW5zYWN0aW9uX2lkIjoyNjYyOTM0MTcxfQ=="
}
```

### WayForPay Callback
```json
{
  "merchantAccount": "test_merch_n1",
  "orderReference": "INV-685099405da029.56636589",
  "merchantSignature": "e3cef51680afe2387699373f8e9ffd38",
  "amount": 10,
  "currency": "USD",
  "authCode": "",
  "email": "test@gmail.com",
  "phone": "49111111111",
  "createdDate": 1750112576,
  "processingDate": 1750112611,
  "cardPan": "41****1111",
  "cardType": "Visa",
  "issuerBankCountry": "Poland",
  "issuerBankName": "CONOTOXIA SP. Z O.O",
  "recToken": "",
  "transactionStatus": "Declined",
  "reason": "Invalid card number",
  "reasonCode": 1105,
  "fee": 0,
  "paymentSystem": "card",
  "acquirerBankName": "WayForPay",
  "cardProduct": "",
  "clientName": "TEST",
  "baseAmount": 416.5,
  "baseCurrency": "UAH"
}
```

## Running Tests

### Prerequisites
- PHP 8.1 or higher
- Composer dependencies installed
- PHPUnit 10.x

### Run All Tests
```bash
composer test
```

### Run Specific Test Suite
```bash
# Run only unit tests
./vendor/bin/phpunit --testsuite="Payment Provider SDK Test Suite"

# Run only integration tests
./vendor/bin/phpunit tests/Integration/

# Run only provider tests
./vendor/bin/phpunit tests/Providers/

# Run only callback tests
./vendor/bin/phpunit tests/Callbacks/
```

### Run Individual Test File
```bash
./vendor/bin/phpunit tests/PaymentManagerTest.php
./vendor/bin/phpunit tests/Providers/LiqPayTest.php
./vendor/bin/phpunit tests/Callbacks/WayForPayCallbackHandlerTest.php
```

### Run with Coverage
```bash
./vendor/bin/phpunit --coverage-html coverage/
```

## Test Utilities

The `TestCase` base class provides common utilities:

- `createMockOrder()` - Creates a mock order for testing
- `createPaymentConfig()` - Creates a payment configuration for testing

## Mocking

Tests use Mockery for mocking dependencies:
- HTTP clients are mocked to avoid real network calls
- Order interfaces are mocked to provide test data
- External dependencies are mocked to isolate units under test

## Assertions

Tests use PHPUnit assertions to verify:
- Return values and types
- Exception throwing
- Object properties and methods
- Array contents and structure
- Boolean conditions

## Best Practices

1. **Arrange-Act-Assert**: All tests follow the AAA pattern
2. **Descriptive Names**: Test method names clearly describe what is being tested
3. **Isolation**: Each test is independent and doesn't rely on other tests
4. **Real Data**: Integration tests use actual callback data from the payment providers
5. **Error Scenarios**: Tests cover both success and failure cases
6. **Documentation**: Tests serve as documentation for the SDK functionality 