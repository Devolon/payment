<?php

namespace Devolon\Payment\Controllers;

use Devolon\Payment\Actions\UpdateTransactionAction;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Requests\PaymentCallbackRequest;
use Devolon\Payment\Resources\TransactionResource;
use Illuminate\Support\Arr;

class HandlePaymentCallbackController
{
    public function __construct(private UpdateTransactionAction $updateTransactionAction)
    {
    }

    public function __invoke(PaymentCallbackRequest $request, Transaction $transaction, string $status)
    {
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => $status,
            'payment_method_data' => Arr::only($request->validated(), array_keys($request->input())),
        ]);

        $transaction = ($this->updateTransactionAction)($transaction, $updateTransactionDTO);

        return TransactionResource::make($transaction);
    }
}
