<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Exceptions\TransactionVerificationFailed;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Events\TransactionDone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Mockery\MockInterface;

class MakeTransactionDoneServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeWithSuccessfulVerify()
    {
        // Arrange
        $transaction = Transaction::factory()->inProcess()->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_DONE,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $gateway = $this->mockPaymentGateway();
        $sut = $this->resolveService();
        Event::fake();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($transaction->payment_method)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('verify')
            ->with($transaction, $updateTransactionDTO->payment_method_data)
            ->once()
            ->andReturn(true);

        // Act
        $result = $sut($transaction, $updateTransactionDTO->payment_method_data);

        // Assert
        Event::assertDispatched(function (TransactionDone $event) use ($transaction, $updateTransactionDTO) {
            return $transaction === $event->transaction &&
                $updateTransactionDTO->payment_method_data === $event->paymentMethodData;
        });
        $this->assertEquals($transaction, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DONE,
        ]);
    }

    public function testInvokeWithoutSuccessfulVerify()
    {
        // Arrange
        $transaction = Transaction::factory()->inProcess()->create();
        $updateTransactionDTO = UpdateTransactionDTO::fromArray([
            'status' => Transaction::STATUS_DONE,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ]);
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $gateway = $this->mockPaymentGateway();
        $sut = $this->resolveService();
        Event::fake();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($transaction->payment_method)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('verify')
            ->with($transaction, $updateTransactionDTO->payment_method_data)
            ->once()
            ->andReturn(false);

        $this->expectException(TransactionVerificationFailed::class);

        // Act
        $sut($transaction, $updateTransactionDTO->payment_method_data);

        // Assert
        Event::assertNotDispatched(TransactionDone::class);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_IN_PROCESS,
        ]);
    }

    private function resolveService(): MakeTransactionDoneService
    {
        return resolve(MakeTransactionDoneService::class);
    }

    private function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    private function mockPaymentGateway(): MockInterface
    {
        return $this->mock(PaymentGatewayInterface::class);
    }
}
