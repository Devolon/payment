<?php

namespace Devolon\Payment\Requests;

use Devolon\Common\Bases\Request;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\GetUpdateTransactionDataRulesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * swagger doc is in virtual directory (SwaggerRequestBodies)
 */
class PaymentCallbackRequest extends Request
{
    protected function postRules(): array
    {
        $validStatuses = [Transaction::STATUS_DONE, Transaction::STATUS_FAILED];

        return [
            'status' => ['required', Rule::in($validStatuses)],
            'transaction' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value->status != Transaction::STATUS_IN_PROCESS) {
                        $fail('Transaction is expired.');
                    }
                }
            ],
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
            $verifyDataRules = $getUpdateTransactionDataRulesService($this->transaction, $this->status);

            $localValidator = Validator::make(Arr::except($this->input(), ['transaction', 'status']), $verifyDataRules);
            $validator->addRules($localValidator->getRules());

            if ($localValidator->fails()) {
                $validator->messages()->merge($localValidator->messages());
            }
        });
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = new JsonResponse($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

        throw new ValidationException($validator, $response);
    }
}
