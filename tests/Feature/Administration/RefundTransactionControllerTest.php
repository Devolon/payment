<?php

namespace Devolon\Payment\Tests\Feature\Administration;

use Devolon\Payment\Gateways\Dummy\DummyGateway;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Actions\Administration\RefundTransactionAction;
use Devolon\Payment\Policies\TransactionPolicy;
use Devolon\Payment\Resources\TransactionResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Mockery\MockInterface;

class RefundTransactionControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'admin.payment.transaction.refund';

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
        $this->assertEquals("/admin/payment/transaction/$transactionId/refund", $result);
    }

    public function test404()
    {
        // Arrange
        $refundTransactionAction = $this->mockRefundTransactionAction();
        $user = $this->getUserClass()::factory()->create();
        $transactionPolicy = $this->mockTransactionPolicy();
        // Expect
        $refundTransactionAction->shouldNotReceive('__invoke');
        $transactionPolicy->shouldNotReceive('refund');

        // Act
        $response = $this
            ->actingAs($user)
            ->putJson(route(self::ROUTE_NAME, ['transaction' => $this->faker->randomNumber()]));

        // Assert
        $response
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testSuccess()
    {
        // Arrange
        $expectedTransaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $refundTransactionAction = $this->mockRefundTransactionAction();
        $transactionPolicy = $this->mockTransactionPolicy();
        $user = $this->getUserClass()::factory()->create();

        // Expect
        $refundTransactionAction
            ->shouldReceive('__invoke')
            ->withArgs(
                function (
                    Transaction $actualTransaction,
                ) use (
                    $expectedTransaction,
                ) {
                    return $actualTransaction->id === $expectedTransaction->id;
                }
            )
            ->once()
            ->andReturn($expectedTransaction)
        ;
        $transactionPolicy->shouldReceive('refund')
            ->withArgs(fn($u, $t) => $u->id === $user->id && $t->id === $expectedTransaction->id)
            ->once()
            ->andReturn(true);

        // Act
        $response = $this
            ->actingAs($user)
            ->putJson(route(self::ROUTE_NAME, ['transaction' => $expectedTransaction->id]));
        

        // Assert
        $response
            ->assertSuccessful()
            ->assertJson(TransactionResource::make($expectedTransaction)->response()->getData(true))
        ;
    }

    public function testUnauthorized()
    {
        // Arrange
        $expectedTransaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $refundTransactionAction = $this->mockRefundTransactionAction();
        $transactionPolicy = $this->mockTransactionPolicy();
        $user = $this->getUserClass()::factory()->create();

        // Expect
        $refundTransactionAction
            ->shouldNotReceive('__invoke');
        ;
        $transactionPolicy->shouldReceive('refund')
            ->withArgs(fn($u, $t) => $u->id === $user->id && $t->id === $expectedTransaction->id)
            ->once()
            ->andReturn(false);

        // Act
        $response = $this
            ->actingAs($user)
            ->putJson(route(self::ROUTE_NAME, ['transaction' => $expectedTransaction->id]));
        

        // Assert
        $response
            ->assertStatus(403)
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

    private function mockRefundTransactionAction(): MockInterface
    {
        return $this->mock(RefundTransactionAction::class);
    }

    private function mockTransactionPolicy(): MockInterface
    {
        return $this->mock(TransactionPolicy::class);
    }
}
