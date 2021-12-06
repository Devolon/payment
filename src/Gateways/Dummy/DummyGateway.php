<?php

namespace Devolon\Payment\Gateways\Dummy;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Contracts\HasPurchaseData;
use Devolon\Payment\Contracts\HasUpdateTransactionData;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Validation\Rule;

class DummyGateway implements PaymentGatewayInterface, HasPurchaseData, HasUpdateTransactionData
{
    public const NAME = 'dummy';

    public function purchase(Transaction $transaction): PurchaseResultDTO
    {
        return PurchaseResultDTO::fromArray([
            'should_redirect' => true,
            'redirect_to' => RedirectDTO::fromArray([
                'redirect_url' =>
                    'https://example.com/payment_redirect?' . http_build_query(['transaction_id' => $transaction->id]),
                'redirect_method' => 'GET',
            ])
        ]);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function purchaseDataRules(): array
    {
        return $this->rules();
    }

    public function updateTransactionDataRules(string $newStatus): array
    {
        return $this->rules();
    }

    private function rules(): array
    {
        return [
            'key' => ['required', Rule::in(['value'])]
        ];
    }

    public function verify(Transaction $transaction, array $data): bool
    {
        return true;
    }
}
