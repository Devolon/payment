<?php

namespace Devolon\Payment\Tests\Unit\Resources;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Resources\TransactionResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

class TransactionResourceTest extends PaymentTestCase
{
    use WithFaker;

    public function testResponse()
    {
        // Arrange
        $transactionData = [
            'product_type' => $this->faker->word,
            'product_id' => $this->faker->randomNumber(),
            'payment_method' => $this->faker->word,
            'money_amount' => $this->faker->randomFloat(2, 0, 99999999.99),
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
        ];
        $transaction = Transaction::factory()->create($transactionData);
        $expectedData = Arr::add($transactionData, 'id', $transaction->id);
        $expectedData = Arr::add(
            $expectedData,
            'created_at',
            $transaction->created_at->format(Setting::DATE_TIME_FORMAT)
        );
        $expectedData = Arr::add(
            $expectedData,
            'updated_at',
            $transaction->updated_at->format(Setting::DATE_TIME_FORMAT)
        );
        $expectedData = Arr::add(
            $expectedData,
            'status',
            $transaction->status,
        );
        $expectedData['count'] = 5;

        // Act
        $result = TransactionResource::make($transaction)->response()->getData(true);

        // Assert
        $this->assertEquals($expectedData, $result);
    }
}
