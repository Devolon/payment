<?php

namespace Devolon\Payment\Tests\Feature;

use Devolon\Payment\Actions\UpdateTransactionAction;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Gateways\Dummy\DummyGateway;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Resources\TransactionResource;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;

class HandlePaymentCallbackControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'app.payment.callback';

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
        $status = $this->faker->word;

        // Act
        $result = route(self::ROUTE_NAME, ['transaction' => $transactionId, 'status' => $status], false);

        // Assert
        $this->assertEquals("/payment/callback/$transactionId/$status", $result);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test422OnGetEndpointWithInvalidData($invalidData)
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $status = $this->faker->randomElement([Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);


        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act

        /** @var TestResponse $response */
        $response = $this
            ->get(
                route(
                    self::ROUTE_NAME,
                    array_merge(['transaction' => $transaction->id, 'status' => $status], $invalidData),
                ),
            );

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test422OnPostEndpointWithInvalidData($invalidData)
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $status = $this->faker->randomElement([Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);


        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act

        /** @var TestResponse $response */
        $response = $this
            ->post(
                route(
                    self::ROUTE_NAME,
                    ['transaction' => $transaction->id, 'status' => $status],
                ),
                $invalidData
            );

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test422OnGetEndpointWithInvalidStatus()
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $status = $this->faker->word;
        $transactionData = $this->transactionData();

        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this
            ->get(
                route(
                    self::ROUTE_NAME,
                    array_merge(['transaction' => $transaction->id, 'status' => $status], $transactionData)
                ),
            );

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test422OnGetEndpointWithInvalidTransaction()
    {
        // Arrange
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transaction = Transaction::factory()->done()->create(['payment_method' => 'dummy']);
        $status = $this->faker->randomElement([Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);
        $transactionData = $this->transactionData();

        // Expect
        $updateTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this
            ->get(
                route(
                    self::ROUTE_NAME,
                    array_merge(['transaction' => $transaction->id, 'status' => $status], $transactionData)
                ),
            );

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testSuccessWithPostMethod()
    {
        // Arrange
        $expectedTransaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transactionData = $this->transactionData();
        $status = $this->faker->randomElement([Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);
        $expectedUpdateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => $status,
            'payment_method_data' => $transactionData,
        ]);

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
            ->post(
                route(
                    self::ROUTE_NAME,
                    ['transaction' => $expectedTransaction->id, 'status' => $status],
                ),
                $transactionData,
            );

        // Assert
        $response
            ->assertSuccessful()
            ->assertJson(TransactionResource::make($expectedTransaction)->response()->getData(true))
        ;
    }

    public function testSuccessWithGetMethod()
    {
        // Arrange
        $expectedTransaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $updateTransactionAction = $this->mockUpdateTransactionAction();
        $transactionData = $this->transactionData();
        $status = $this->faker->randomElement([Transaction::STATUS_DONE, Transaction::STATUS_FAILED]);
        $expectedUpdateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => $status,
            'payment_method_data' => $transactionData,
        ]);

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
            ->get(
                route(
                    self::ROUTE_NAME,
                    array_merge(['transaction' => $expectedTransaction->id, 'status' => $status], $transactionData),
                ),
            );

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
            'gateway has wrong key on post' => [
                [$faker->word => 'value'],
            ],
            'payment_method_data has wrong value on post' => [
                ['key' => $faker->word],
            ],
            'gateway has wrong key on get' => [
                [$faker->word => 'value'],
            ],
            'payment_method_data has wrong value on get' => [
                ['key' => $faker->word],
            ],
        ];
    }

    private function transactionData(): array
    {
        return [
            'key' => 'value',
        ];
    }

    private function mockUpdateTransactionAction(): MockInterface
    {
        return $this->mock(UpdateTransactionAction::class);
    }
}
