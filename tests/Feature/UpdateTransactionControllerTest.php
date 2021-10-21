<?php

namespace Devolon\Payment\Tests\Feature;

use Devolon\Payment\Gateways\Dummy\DummyGateway;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Actions\UpdateTransactionAction;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Resources\TransactionResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Mockery\MockInterface;

class UpdateTransactionControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'app.payment.transaction.update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->tag(DummyGateway::class, PaymentGatewayInterface::class);
    }

    /**
     * @group app
     * @group transaction
     * @group route
     * @group success
     */
    public function testRoute()
    {
        // Arrange
        $transactionId = $this->faker->randomNumber();
        // Act
        $result = route(self::ROUTE_NAME, ['transaction' => $transactionId], false);

        // Assert
        $this->assertEquals("/payment/transaction/$transactionId", $result);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test422WithInvalidData($invalidFieldName, $invalidValue, $expectedValidationErrorKey)
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $user = $this->getUserClass()::factory()->create();
        $transaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $transactionData = $this->transactionData();
        Arr::set($transactionData, $invalidFieldName, $invalidValue);

        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this
            ->actingAs($user)
            ->patchJson(route(self::ROUTE_NAME, ['transaction' => $transaction->id]), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([$expectedValidationErrorKey])
            ->assertJsonMissingValidationErrors(
                array_diff(array_keys($transactionData), [$expectedValidationErrorKey])
            );
    }

    public function test404()
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $user = $this->getUserClass()::factory()->create();
        $transactionData = $this->transactionData();

        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this
            ->actingAs($user)
            ->patchJson(route(self::ROUTE_NAME, ['transaction' => $this->faker->randomNumber()]), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testSuccess()
    {
        // Arrange
        $expectedTransaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $user = $this->getUserClass()::factory()->create();
        $transactionData = $this->transactionData();
        $expectedUpdateTransactionDTO = UpdateTransactionDTO::fromArray($transactionData);

        // Expect
        $updateTransactionAction
            ->shouldReceive('__invoke')
            ->withArgs(
                function (
                    Transaction $actualTransaction,
                    UpdateTransactionDTO $actualUpdateTransactionDTO,
                ) use (
                    $expectedTransaction,
                    $expectedUpdateTransactionDTO,
                ) {
                    return $actualUpdateTransactionDTO->toArray() === $expectedUpdateTransactionDTO->toArray() &&
                        $actualTransaction->id === $expectedTransaction->id;
                }
            )
            ->once()
            ->andReturn($expectedTransaction)
        ;

        // Act
        $response = $this
            ->actingAs($user)
            ->patchJson(route(self::ROUTE_NAME, ['transaction' => $expectedTransaction->id]), $transactionData);

        // Assert
        $response
            ->assertSuccessful()
            ->assertJson(TransactionResource::make($expectedTransaction)->response()->getData(true))
        ;
    }

    public function invalidDataProvider(): array
    {
        $faker = $this->makeFaker('en_US');

        return [
            'status is invalid' => [
                'status',
                $faker->word,
                'status'
            ],
            'payment_method_data is not array' => [
                'payment_method_data',
                $faker->word,
                'payment_method_data'
            ],
            'payment_method_data has wrong key' => [
                'payment_method_data',
                [$faker->word => 'value'],
                'payment_method_data.key'
            ],
            'payment_method_data has wrong value' => [
                'payment_method_data',
                ['key' => $faker->word],
                'payment_method_data.key'
            ],
        ];
    }

    public function transactionData()
    {
        return [
            'status' => $this->faker->randomElement(Transaction::STATUSES),
            'payment_method_data' => [
                'key' => 'value',
            ],
        ];
    }

    private function mockUpdateTransactionAction(): MockInterface
    {
        return $this->mock(UpdateTransactionAction::class);
    }
}
