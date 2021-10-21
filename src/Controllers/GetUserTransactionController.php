<?php

namespace Devolon\Payment\Controllers;

use Devolon\Common\Bases\Controller;
use Devolon\Payment\Actions\GetUserTransactionListAction;
use Devolon\Payment\Resources\TransactionCollection;
use Illuminate\Http\Request;

/**
 * swagger doc is in virtual directory (SwaggerPaths)
 */
class GetUserTransactionController extends Controller
{
    public function __construct(private GetUserTransactionListAction $getUserTransactionListAction)
    {
    }

    public function __invoke(Request $request): TransactionCollection
    {
        $user = auth()->user();
        $userTicketInstances = ($this->getUserTransactionListAction)(
            $user->getAuthIdentifier(),
            $request->query('perPage')
        );

        return TransactionCollection::make($userTicketInstances);
    }
}
