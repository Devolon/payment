<?php

namespace Devolon\Payment\Resources;

use Carbon\Carbon;
use Devolon\Common\Tools\Setting;
use Devolon\Common\Bases\Resource;

/**
 * swagger doc is in virtual directory (SwaggerResponses)
 *
 * @property int id
 * @property string status
 * @property string product_type
 * @property int product_id
 * @property string payment_method
 * @property float money_amount
 * @property array payment_method_data
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class TransactionResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'product_type' => $this->product_type,
            'product_id' => $this->product_id,
            'count' => 5,
            'payment_method' => $this->payment_method,
            'money_amount' => $this->money_amount,
            'payment_method_data' => $this->payment_method_data,
            'created_at' => $this->created_at->format(Setting::DATE_TIME_FORMAT),
            'updated_at' => $this->updated_at->format(Setting::DATE_TIME_FORMAT),
        ];
    }
}
