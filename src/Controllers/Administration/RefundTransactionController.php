<?php

namespace Devolon\Payment\Controllers\Administration;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Actions\RefundTransactionAction;
use Devolon\Payment\Resources\TransactionResource;
use Illuminate\Support\Facades\Gate;


/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class RefundTransactionController 
{
    public function __construct(private RefundTransactionAction $refundTransactionAction)
    {
    }

    public function __invoke(Transaction $transaction): TransactionResource
    {
        Gate::authorize('refund', $transaction);
        $transaction = ($this->refundTransactionAction)($transaction);

        return TransactionResource::make($transaction);
    }
}
