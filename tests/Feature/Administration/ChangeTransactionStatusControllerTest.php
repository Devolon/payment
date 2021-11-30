<?php

namespace Devolon\Payment\Tests\Feature\Administration;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mockery\MockInterface;
use Devolon\Payment\Actions\Administration\ChangeTransactionStatusAction;

class ChangeTransactionStatusControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'admin.payment.transaction.status.update';

    public function testRoute()
    {
        // Arrange
        $transactionId = $this->faker->randomNumber(5);

        // Act
        $result = route(self::ROUTE_NAME, ['transaction' => $transactionId], false);

        // Assert
        $this->assertEquals("/admin/payment/transaction/$transactionId/status", $result);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test422WithInvalidData(array $invalidData)
    {
        // Arrange
        $transaction = Transaction::factory()->create();
        $changeTransactionStatusAction = $this->mockChangeTransactionStatusAction();

        // Expect
        $changeTransactionStatusAction->shouldNotReceive('__invoke');

        // Act
        $response = $this
            ->putJson(route(self::ROUTE_NAME, ['transaction' => $transaction->id]), $invalidData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['value'])
            ->assertJsonMissingValidationErrors([]);
    }

    /**
     * @dataProvider statusTransitions
     */
    public function testSuccess(string $previousStatus, string $nextStatus)
    {
        // Arrange
        $changeTransactionStatusAction = $this->mockChangeTransactionStatusAction();
        $transaction = Transaction::factory()->create(['status' => $previousStatus]);

        // Expect
        $changeTransactionStatusAction
            ->shouldReceive('__invoke')
            ->withArgs(function (Transaction $actualTransaction, string $actualStatus) use ($transaction, $nextStatus) {
                return $transaction->id === $actualTransaction->id && $actualStatus === $nextStatus;
            })
            ->once()
        ;

        // Act
        $response = $this
            ->putJson(route(self::ROUTE_NAME, ['transaction' => $transaction->id]), ['value' => $nextStatus]);

        // Assert
        $response
            ->assertNoContent();
    }

    private function mockChangeTransactionStatusAction(): MockInterface
    {
        return $this->mock(ChangeTransactionStatusAction::class);
    }

    public function statusTransitions()
    {
        return [
            'refund' => [Transaction::STATUS_DONE, Transaction::STATUS_REFUNDED],
        ];
    }

    public function invalidDataProvider(): array
    {
        $faker = $this->makeFaker('en_US');

        return [
            'value is missing' => [[]],
            'value is null' => [['value' => null]],
            'value is an invalid value' => [['value' => $faker->word]],
        ];
    }
}
