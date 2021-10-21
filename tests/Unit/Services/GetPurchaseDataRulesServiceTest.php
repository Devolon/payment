<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\HasPurchaseData;
use Devolon\Payment\Services\GetPurchaseDataRulesService;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;

class GetPurchaseDataRulesServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeWhenGatewayDoesNotImplementHasPurchaseData()
    {
        // Arrange
        $gateway = $this->mockPaymentGateway();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $service = $this->resolveService();
        $paymentMethod = $this->faker->word;

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($paymentMethod)
            ->once()
            ->andReturn($gateway);

        $gateway->shouldNotReceive('purchaseDataRules');

        // Act
        $result = $service($paymentMethod);

        // Assert
        $this->assertEquals([], $result);
    }

    public function testInvokeWhenGatewayImplementHasPurchaseData()
    {
        // Arrange
        $gateway = $this->mockPaymentGatewayAndHasPurchaseData();
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $service = $this->resolveService();
        $paymentMethod = $this->faker->word;
        $purchaseDataRules = [
            $this->faker->word => $this->faker->word,
        ];

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($paymentMethod)
            ->once()
            ->andReturn($gateway);

        $gateway
            ->shouldReceive('purchaseDataRules')
            ->withNoArgs()
            ->once()
            ->andReturn($purchaseDataRules);

        // Act
        $result = $service($paymentMethod);

        // Assert
        $this->assertEquals($purchaseDataRules, $result);
    }

    private function resolveService()
    {
        return resolve(GetPurchaseDataRulesService::class);
    }

    private function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    private function mockPaymentGateway(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class);
    }

    private function mockPaymentGatewayAndHasPurchaseData(): MockInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class, HasPurchaseData::class);
    }
}
