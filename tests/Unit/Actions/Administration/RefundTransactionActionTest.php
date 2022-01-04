<?php

namespace Devolon\Payment\Tests\Unit\Actions\Administration;

use Devolon\Payment\Actions\Administration\RefundTransactionAction;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\MakeTransactionRefundedService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class RefundTransactionActionTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $mockTransactionRefundedService = $this->mockMakeTransactionRefundedService();
        $transaction = Transaction::factory()->create();
        $returnTransaction = Transaction::factory()->create();
        $action = $this->resolveAction();

        // Expect
        $mockTransactionRefundedService
            ->shouldReceive('__invoke')
            ->withArgs([ $transaction])
            ->once()
            ->andReturn($returnTransaction);

        // Act
        $result = $action($transaction);

        // Assert
        $this->assertEquals($returnTransaction, $result);
    }

    public function mockMakeTransactionRefundedService(): MockInterface
    {
        return $this->mock(MakeTransactionRefundedService::class);
    }

    public function resolveAction(): RefundTransactionAction
    {
        return resolve(RefundTransactionAction::class);
    }
}
