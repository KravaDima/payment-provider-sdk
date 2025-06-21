<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\DTO;

use Checkvin\PaymentProviderSdk\Contracts\CallbackDataInterface;

final class WayForPayCallbackData implements CallbackDataInterface
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->data['transactionStatus'] === 'Approved';
    }

    public function getOrderId(): string
    {
        return $this->data['orderReference'];
    }

    public function getAmount(): string
    {
        return (string) $this->data['amount'];
    }

    public function getTransactionStatus(): string
    {
        return $this->data['transactionStatus'];
    }

    public function getMerchantAccount(): string
    {
        return $this->data['merchantAccount'];
    }

    public function getCurrency(): string
    {
        return $this->data['currency'];
    }

    public function getAuthCode(): string
    {
        return $this->data['authCode'];
    }

    public function getEmail(): string
    {
        return $this->data['email'];
    }

    public function getPhone(): string
    {
        return $this->data['phone'];
    }

    public function getCreatedDate(): int
    {
        return (int) $this->data['createdDate'];
    }

    public function getProcessingDate(): int
    {
        return (int) $this->data['processingDate'];
    }

    public function getCardPan(): string
    {
        return $this->data['cardPan'];
    }

    public function getCardType(): string
    {
        return $this->data['cardType'];
    }

    public function getIssuerBankCountry(): string
    {
        return $this->data['issuerBankCountry'];
    }

    public function getIssuerBankName(): string
    {
        return $this->data['issuerBankName'];
    }

    public function getRecToken(): string
    {
        return $this->data['recToken'];
    }

    public function getReason(): string
    {
        return $this->data['reason'];
    }

    public function getReasonCode(): string
    {
        return $this->data['reasonCode'];
    }

    public function getFee(): float
    {
        return (float) $this->data['fee'];
    }

    public function getPaymentSystem(): string
    {
        return $this->data['paymentSystem'];
    }

    public function getRawData(): array
    {
        return $this->data;
    }
} 