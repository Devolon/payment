<?php

namespace Devolon\Payment\Controllers\Administration;

use Devolon\Common\Bases\Controller;
use Devolon\Payment\Actions\Administration\GetTransactionListAction;
use Devolon\Payment\Resources\TransactionCollection;
use Illuminate\Http\Request;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class GetTransactionsController extends Controller
{
    public function __construct(private GetTransactionListAction $getTransactionListAction)
    {
    }

    public function __invoke(Request $request): TransactionCollection
    {
        $transactions = ($this->getTransactionListAction)($request->query('perPage'));

        return TransactionCollection::make($transactions);
    }
}
