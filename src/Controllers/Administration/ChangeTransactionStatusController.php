<?php

namespace Devolon\Payment\Controllers\Administration;

use Devolon\Payment\Actions\Administration\ChangeTransactionStatusAction;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Requests\Administration\ChangeTransactionStatusRequest;
use Illuminate\Http\Response;

class ChangeTransactionStatusController
{
    public function __construct(private ChangeTransactionStatusAction $changeTransactionStatusAction)
    {
    }

    public function __invoke(Transaction $transaction, ChangeTransactionStatusRequest $request): Response
    {
        ($this->changeTransactionStatusAction)($transaction, $request->value);

        return response()->noContent();
    }
}