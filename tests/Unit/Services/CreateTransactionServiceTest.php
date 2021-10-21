<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Services\CreateTransactionService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class CreateTransactionServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $price = 123.45;
        $user = $this->getUserClass()::factory()->create();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            'user_id' => $user->id,
            'payment_method' => $this->faker->word,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
            'product_type' => StubProduct::class,
        ]);
        Payment::addProductType(StubProduct::class);

        $sut = $this->resolveService();

        // Act
        $result = $sut($createTransactionDTO);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_IN_PROCESS,
            'payment_method' => $createTransactionDTO->payment_method,
            'money_amount' => $price,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($createTransactionDTO->payment_method_data, $result->payment_method_data);
    }

    private function resolveService(): CreateTransactionService
    {
        return resolve(CreateTransactionService::class);
    }
}
