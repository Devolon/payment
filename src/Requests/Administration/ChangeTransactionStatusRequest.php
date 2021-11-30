<?php

namespace Devolon\Payment\Requests\Administration;

use Devolon\Common\Bases\Request;
use Devolon\Payment\Models\Transaction;
use Illuminate\Validation\Rule;

class ChangeTransactionStatusRequest extends Request
{
    protected function putRules(): array
    {
        return [
            'value' => ['required', Rule::in([Transaction::STATUS_REFUNDED])]
        ];
    }
}