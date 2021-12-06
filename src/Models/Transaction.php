<?php

namespace Devolon\Payment\Models;

use Carbon\Carbon;
use Devolon\Common\Traits\RepositoryRouteBinding;
use Devolon\Payment\Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int id
 * @property string status
 * @property string payment_method
 * @property float money_amount
 * @property int user_id
 * @property array payment_method_data
 * @property array gateway_results
 * @property string product_type
 * @property int product_id
 * @property Model product
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @method static TransactionFactory factory(...$parameters)
 */
class Transaction extends Model
{
    use HasFactory;
    use RepositoryRouteBinding;

    public const STATUS_IN_PROCESS = 'in_process';
    public const STATUS_DONE = 'done';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    public const STATUSES = [
        self::STATUS_IN_PROCESS,
        self::STATUS_DONE,
        self::STATUS_FAILED,
        self::STATUS_REFUNDED,
    ];

    protected $casts = [
        'payment_method_data' => 'array',
        'gateway_results' => 'array',
        'product_data' => 'array',
    ];

    public function product(): MorphTo
    {
        return $this->morphTo('product');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}
