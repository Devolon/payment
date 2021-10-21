<?php

namespace Devolon\Payment\Requests;

use Devolon\Common\Bases\Request;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\GetUpdateTransactionDataRulesService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * swagger doc is in virtual directory (SwaggerRequestBodies)
 */
class UpdateTransactionRequest extends Request
{
    protected function postRules(): array
    {
        $validStatuses = Arr::except(Transaction::STATUSES, Transaction::STATUS_IN_PROCESS);

        return [
            'status' => ['required', Rule::in($validStatuses)],
            'payment_method_data' => ['filled', 'array'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }

            /** @var GetUpdateTransactionDataRulesService $getUpdateTransactionDataRulesService */
            $getUpdateTransactionDataRulesService = app(GetUpdateTransactionDataRulesService::class);
            $verifyDataRules = $getUpdateTransactionDataRulesService($this->transaction, $this->input('status'));

            $prefixedRules = self::appendPrefixToArrayKeys($verifyDataRules, 'payment_method_data.');

            Validator::make($this->input(), $prefixedRules)->validate();
        });
    }
}
