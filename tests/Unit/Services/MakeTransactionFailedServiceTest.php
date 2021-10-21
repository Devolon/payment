<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Services\MakeTransactionFailedService;
use Devolon\Payment\Events\TransactionFailed;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;

class MakeTransactionFailedServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $transaction = Transaction::factory()->inProcess()->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_FAILED,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $sut = $this->resolveService();
        Event::fake();

        // Act
        $result = $sut($transaction, $updateTransactionDTO->payment_method_data);

        // Assert
        Event::assertDispatched(function (TransactionFailed $event) use ($transaction, $updateTransactionDTO) {
            return $transaction === $event->transaction &&
                $updateTransactionDTO->payment_method_data === $event->paymentMethodData;
        });
        $this->assertEquals($transaction, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_FAILED,
        ]);
    }

    private function resolveService(): MakeTransactionFailedService
    {
        return resolve(MakeTransactionFailedService::class);
    }
}
