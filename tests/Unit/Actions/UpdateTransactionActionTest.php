<?php

namespace Devolon\Payment\Tests\Unit\Actions;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Actions\UpdateTransactionAction;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Services\MakeTransactionFailedService;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class UpdateTransactionActionTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeMakeTransactionDone()
    {
        // Arrange
        $makeTransactionDoneService = $this->mockMakeTransactionDoneService();
        $transaction = Transaction::factory()->inProcess()->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_DONE,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $action = $this->resolveAction();

        // Expect
        $makeTransactionDoneService
            ->shouldReceive('__invoke')
            ->with($transaction, $updateTransactionDTO->payment_method_data)
            ->once()
            ->andReturn($transaction);

        // Act
        $result = $action($transaction, $updateTransactionDTO);

        // Assert
        $this->assertEquals($transaction, $result);
    }

    /**
     * @testWith ["done"]
     *           ["failed"]
     */
    public function testInvokeThrowsExceptionWhenStatusIsNotInProcess(string $status)
    {
        // Arrange
        $makeTransactionDoneService = $this->mockMakeTransactionDoneService();
        $transaction = Transaction::factory([
            'status' => $status
        ])->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_DONE,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $action = $this->resolveAction();

        // Expect
        $this->expectException(\InvalidArgumentException::class);
        $makeTransactionDoneService->shouldNotReceive('__invoke');

        // Act
        $action($transaction, $updateTransactionDTO);
    }

    public function testInvokeMakeTransactionFailed()
    {
        // Arrange
        $makeTransactionFailedService = $this->mockMakeTransactionFailedService();
        $transaction = Transaction::factory()->inProcess()->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_FAILED,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $action = $this->resolveAction();

        // Expect
        $makeTransactionFailedService
            ->shouldReceive('__invoke')
            ->with($transaction, $updateTransactionDTO->payment_method_data)
            ->once()
            ->andReturn($transaction);

        // Act
        $result = $action($transaction, $updateTransactionDTO);

        // Assert
        $this->assertEquals($transaction, $result);
    }

    public function mockMakeTransactionDoneService(): MockInterface
    {
        return $this->mock(MakeTransactionDoneService::class);
    }

    public function mockMakeTransactionFailedService(): MockInterface
    {
        return $this->mock(MakeTransactionFailedService::class);
    }

    private function resolveAction(): UpdateTransactionAction
    {
        return resolve(UpdateTransactionAction::class);
    }
}
