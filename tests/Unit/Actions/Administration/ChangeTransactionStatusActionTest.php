<?php

namespace Devolon\Payment\Tests\Unit\Actions\Administration;

use Devolon\Payment\Actions\Administration\ChangeTransactionStatusAction;
use Devolon\Payment\Services\RefundTransactionService;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use LogicException;
use Mockery\MockInterface;

class ChangeTransactionStatusActionTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeForRefund()
    {
        // Arrange
        $refundTransactionService = $this->mockRefundTransactionService();
        $action = $this->resolveAction();
        $transaction = Transaction::factory()->create(['status' => Transaction::STATUS_DONE]);

        // Expect
        $refundTransactionService
            ->shouldReceive('__invoke')
            ->with($transaction, Transaction::STATUS_REFUNDED)
            ->once();

        // Act
        $action($transaction, Transaction::STATUS_REFUNDED);
    }

    public function testInvokeWithInvalidStatus()
    {
        // Arrange
        $refundTransactionService = $this->mockRefundTransactionService();
        $action = $this->resolveAction();
        $transaction = Transaction::factory()->create(['status' => Transaction::STATUS_DONE]);
        $nextStatus = $this->faker->word;

        // Expect
        $refundTransactionService->shouldNotReceive('__invoke');
        $this->expectException(LogicException::class);

        // Act
        $action($transaction, $nextStatus);
    }

    public function provideTransitions(): array
    {
        return [
            'refund' => [Transaction::STATUS_DONE, Transaction::STATUS_REFUNDED],
        ];
    }

    private function mockRefundTransactionService(): MockInterface
    {
        return $this->mock(RefundTransactionService::class);
    }

    private function resolveAction(): ChangeTransactionStatusAction
    {
        return resolve(ChangeTransactionStatusAction::class);
    }
}