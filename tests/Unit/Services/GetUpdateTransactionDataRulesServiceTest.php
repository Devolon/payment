<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Contracts\HasUpdateTransactionData;
use Devolon\Payment\Services\GetUpdateTransactionDataRulesService;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;

class GetUpdateTransactionDataRulesServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeWhenGatewayDoesNotImplementHasVerifyData()
    {
        // Arrange
        $paymentMethod = $this->faker->word;
        $newStatus = $this->faker->word;
        $transaction = Transaction::factory()->inProcess()->create([
            'payment_method' => $paymentMethod
        ]);
        $gateway = $this->mockPaymentGateway();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $service = $this->resolveService();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($paymentMethod)
            ->once()
            ->andReturn($gateway);

        $gateway->shouldNotReceive('updateTransactionDataRules');

        // Act
        $result = $service($transaction, $newStatus);

        // Assert
        $this->assertEquals([], $result);
    }

    public function testInvokeWhenGatewayImplementHasVerifyData()
    {
        // Arrange
        $paymentMethod = $this->faker->word;
        $newStatus = $this->faker->word;
        $transaction = Transaction::factory()->inProcess()->create([
            'payment_method' => $paymentMethod
        ]);
        $gateway = $this->mockPaymentGatewayAndHasVerifyData();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $service = $this->resolveService();
        $verifyDataRules = [
            $this->faker->word => $this->faker->word,
        ];

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($paymentMethod)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('updateTransactionDataRules')
            ->with($newStatus)
            ->once()
            ->andReturn($verifyDataRules);

        // Act
        $result = $service($transaction, $newStatus);

        // Assert
        $this->assertEquals($verifyDataRules, $result);
    }

    private function resolveService()
    {
        return resolve(GetUpdateTransactionDataRulesService::class);
    }

    private function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    private function mockPaymentGateway(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class);
    }

    private function mockPaymentGatewayAndHasVerifyData(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class, HasUpdateTransactionData::class);
    }
}
