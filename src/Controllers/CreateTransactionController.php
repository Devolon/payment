<?php

namespace Devolon\Payment\Controllers;

use Devolon\Payment\Actions\CreateTransactionAction;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Requests\CreateTransactionRequest;
use Devolon\Payment\Resources\TransactionResultResource;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class CreateTransactionController
{
    public function __construct(private CreateTransactionAction $createTransactionAction)
    {
    }

    public function __invoke(CreateTransactionRequest $request): TransactionResultResource
    {
        $user = auth()->user();
        $createTransactionDTO = CreateTransactionDTO::fromArray(
            array_merge(
                $request->validated(),
                [
                    'user_id' => $user->id,
                ],
            )
        );

        $transactionResultDTO = ($this->createTransactionAction)($createTransactionDTO);

        return TransactionResultResource::make($transactionResultDTO);
    }
}
