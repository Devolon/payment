<?php

namespace Devolon\Payment\Gateways\Dummy;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Contracts\HasPurchaseData;
use Devolon\Payment\Contracts\HasUpdateTransactionData;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Validation\Rule;

class DummyTwoGateway extends DummyGateway
{
    public const NAME = 'dummy_two';
}
