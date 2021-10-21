<?php

namespace Devolon\Payment\Controllers;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Actions\UpdateTransactionAction;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Requests\UpdateTransactionRequest;
use Devolon\Payment\Resources\TransactionResource;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class UpdateTransactionController
{
    public function __construct(private UpdateTransactionAction $updateTransactionAction)
    {
    }

    public function __invoke(Transaction $transaction, UpdateTransactionRequest $request): TransactionResource
    {
        $updateTransactionDTO = UpdateTransactionDTO::fromArray($request->validated());

        $transaction = ($this->updateTransactionAction)($transaction, $updateTransactionDTO);

        return TransactionResource::make($transaction);
    }
}
