<?php

namespace Devolon\Payment\Repositories;

use Devolon\Common\Bases\Repository;
use Devolon\Common\Tools\Setting;
use Devolon\Payment\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository extends Repository
{
    protected array $fillable = [
        'status',
        'payment_method',
        'money_amount',
        'user_id',
        'payment_method_data',
        'product_type',
        'product_id',
        'gateway_results',
    ];

    public function getPaginatedForUser(int $userId, int $perPage = Setting::PAGE_SIZE): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->where('status', Transaction::STATUS_DONE)
            ->paginate($perPage);
    }

    public function hasProduct(Transaction $transaction): bool
    {
        return $transaction->product()->exists();
    }

    public function model(): string
    {
        return Transaction::class;
    }
}
