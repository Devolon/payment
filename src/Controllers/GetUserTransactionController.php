<?php

namespace Devolon\Payment\Controllers;

use Devolon\Common\Bases\Controller;
use Devolon\Payment\Actions\GetUserTransactionListAction;
use Devolon\Payment\Requests\GetUserTransactionRequest;
use Devolon\Payment\Resources\TransactionCollection;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class GetUserTransactionController extends Controller
{
    public function __construct(private GetUserTransactionListAction $getUserTransactionListAction)
    {
    }

    public function __invoke(GetUserTransactionRequest $request): TransactionCollection
    {
        $user = auth()->user();
        $transactions = ($this->getUserTransactionListAction)(
            $user->getAuthIdentifier(),
            $request->query('perPage'),
            $request->query('statuses'),
        );

        return TransactionCollection::make($transactions);
    }
}
