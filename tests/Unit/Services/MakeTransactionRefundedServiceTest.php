<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Services\MakeTransactionRefundedService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class MakeTransactionRefundedServiceTest extends PaymentTestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Payment::clearProductTypes();
        parent::tearDown();
    }

    /**
     * @deprecated
     */
    public function testInvoke()
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'status' => Transaction::STATUS_DONE,
        ]);
        $service = $this->resolveService();

        // Act
        $result = $service($transaction);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_REFUNDED,
        ]);
        $this->assertDatabaseMissing('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_DONE,
        ]);
    }

    private function resolveService(): MakeTransactionRefundedService
    {
        return resolve(MakeTransactionRefundedService::class);
    }

}
