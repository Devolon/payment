<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\CanRefund;
use Devolon\Payment\Events\TransactionRefunded;
use Devolon\Payment\Exceptions\RefundTransactionFailed;
use Devolon\Payment\Exceptions\TransactionCanNotGetRefunded;
use Devolon\Payment\Exceptions\TransactionVerificationFailed;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Services\RefundTransactionService;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\DTOs\UpdateTransactionDTO;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Events\TransactionDone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use LogicException;
use Mockery;
use Mockery\MockInterface;

class RefundTransactionServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeWithSuccessfulRefund()
    {
        // Arrange
        $transaction = Transaction::factory()->done()->create();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $gateway = $this->mockPaymentGatewayThatCanRefund();
        $sut = $this->resolveService();
        Event::fake();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($transaction->payment_method)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('refund')
            ->with($transaction)
            ->once()
            ->andReturn(true);

        // Act
        $result = $sut($transaction);

        // Assert
        Event::assertDispatched(function (TransactionRefunded $event) use ($transaction) {
            return $transaction === $event->transaction;
        });
        $this->assertEquals($transaction, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_REFUNDED,
        ]);
    }

    public function testInvokeForGatewayThatDoesNotSupportRefund()
    {
        // Arrange
        $transaction = Transaction::factory()->done()->create();
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

        $gateway->shouldNotReceive('refund');
        $this->expectException(TransactionCanNotGetRefunded::class);

        // Act
        $result = $sut($transaction);

        // Assert
        Event::assertNotDispatched(TransactionRefunded::class);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DONE,
        ]);
    }

    public function testInvokeWithoutSuccessfulVerify()
    {
        // Arrange
        $transaction = Transaction::factory()->done()->create();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $gateway = $this->mockPaymentGatewayThatCanRefund();
        $sut = $this->resolveService();
        Event::fake();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($transaction->payment_method)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('refund')
            ->with($transaction)
            ->once()
            ->andReturn(false);

        $this->expectException(RefundTransactionFailed::class);

        // Act
        $sut($transaction);

        // Assert
        Event::assertNotDispatched(TransactionRefunded::class);
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => Transaction::STATUS_DONE,
        ]);
    }

    private function resolveService(): RefundTransactionService
    {
        return resolve(RefundTransactionService::class);
    }

    private function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    private function mockPaymentGatewayThatCanRefund(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class, CanRefund::class);
    }

    private function mockPaymentGateway(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class);
    }
}
