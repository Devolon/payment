<?php

namespace Devolon\Payment\Requests;

use Devolon\Common\Bases\Request;
use Devolon\Payment\Models\Transaction;
use Illuminate\Validation\Rule;

/**
 * swagger doc is in virtual directory (SwaggerRequestBodies)
 */
class GetUserTransactionRequest extends Request
{
    protected function getRules(): array
    {
        return [
            'statuses' => ['nullable', 'array', 'min:1'],
            'statuses.*' => ['filled', 'string', Rule::in(Transaction::STATUSES)],
        ];
    }
}
