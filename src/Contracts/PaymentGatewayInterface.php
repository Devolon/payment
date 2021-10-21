<?php

namespace Devolon\Payment\Contracts;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;

interface PaymentGatewayInterface
{
    public function purchase(Transaction $transaction): PurchaseResultDTO;
    public function verify(Transaction $transaction, array $data): bool;
    public function getName(): string;
}
