<?php

declare(strict_types=1);

namespace Checkvin\PaymentProviderSdk\DTO;

use Checkvin\PaymentProviderSdk\Contracts\CallbackDataInterface;

final class LiqPayCallbackData implements CallbackDataInterface
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->data['status'] === 'success';
    }

    public function getOrderId(): string
    {
        return $this->data['order_id'];
    }

    public function getAmount(): string
    {
        return (string) $this->data['amount'];
    }

    public function getTransactionStatus(): string
    {
        return $this->data['status'];
    }

    public function getPaymentId(): int
    {
        return (int) $this->data['payment_id'];
    }

    public function getTransactionId(): string
    {
        return $this->data['transaction_id'];
    }

    public function getSenderCardMask(): string
    {
        return $this->data['sender_card_mask2'];
    }

    public function getSenderCardBank(): string
    {
        return $this->data['sender_card_bank'];
    }

    public function getSenderCardType(): string
    {
        return $this->data['sender_card_type'];
    }

    public function getSenderCardCountry(): int
    {
        return (int) $this->data['sender_card_country'];
    }

    public function getIp(): string
    {
        return $this->data['ip'];
    }

    public function getDescription(): string
    {
        return $this->data['description'];
    }

    public function getAction(): string
    {
        return $this->data['action'];
    }

    public function getPaymentType(): string
    {
        return $this->data['paytype'];
    }

    public function getLanguage(): string
    {
        return $this->data['language'];
    }

    public function getCreateDate(): int
    {
        return (int) $this->data['create_date'];
    }

    public function getEndDate(): int
    {
        return (int) $this->data['end_date'];
    }

    public function getRawData(): array
    {
        return $this->data;
    }
}
