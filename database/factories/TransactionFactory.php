<?php

namespace Devolon\Payment\Database\Factories;

use Devolon\Payment\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method Transaction|Collection|Transaction[] make($attributes = [], ?Model $parent = null)
 * @method Transaction|Collection|Transaction[] create($attributes = [], ?Model $parent = null)
 */
class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $definition = [
            'status' => $this->faker->randomElement(Transaction::STATUSES),
            'payment_method' => $this->faker->word,
            'money_amount' => $this->faker->randomFloat(2, 0, 99999999.99),
            'product_type' => $this->faker->word,
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];

        $provider = config('auth.guards.api.provider');
        /** @var string $userModel */
        $userModel = config('auth.providers.' . $provider . '.model');

        if (method_exists($userModel, 'factory')) {
            $definition['user_id'] = $userModel::factory();
        }

        return $definition;
    }

    public function inProcess(): self
    {
        return $this->state([
            'status' => Transaction::STATUS_IN_PROCESS,
        ]);
    }

    public function done(): self
    {
        return $this->state([
            'status' => Transaction::STATUS_DONE,
        ]);
    }

    public function failed(): self
    {
        return $this->state([
            'status' => Transaction::STATUS_FAILED,
        ]);
    }
}
