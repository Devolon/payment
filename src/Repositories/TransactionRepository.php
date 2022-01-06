<?php

namespace Devolon\Payment\Repositories;

use Devolon\Common\Bases\Repository;
use Devolon\Common\Tools\Setting;
use Devolon\Payment\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
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
        'product_data',
    ];

    public function getPaginatedForUser(int $userId, int $perPage = Setting::PAGE_SIZE, ?array $statuses = null): LengthAwarePaginator
    {
        return $this->query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->when(
                !$statuses,
                fn(Builder $q) => $q->where('status', Transaction::STATUS_DONE),
                fn(Builder $q) => $q->whereIn('status', $statuses),
            )
            ->paginate($perPage);
    }

    public function getPaginated(int $perPage = Setting::PAGE_SIZE): LengthAwarePaginator
    {
        return $this->query()
            ->orderBy('created_at', 'desc')
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
