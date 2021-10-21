<?php

namespace Devolon\Payment\Tests\Unit\Actions;

use Devolon\Payment\Actions\GetPaymentGatewaysAction;
use Devolon\Payment\DTOs\PaymentGatewayListDTO;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class GetPaymentGatewaysActionTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $nameRange = range('A', 'Z');
        $gateways = $this->faker->randomElements(
            $nameRange,
            $this->faker->numberBetween(1, count($nameRange))
        );

        $paymentGatewayListDTO = PaymentGatewayListDTO::fromArray([
            'payment_gateways' => $gateways
        ]);

        $action = $this->resolveAction();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('getAllNames')
            ->withNoArgs()
            ->once()
            ->andReturn($gateways);

        // Act
        $result = $action();

        // Assert
        $this->assertEquals($paymentGatewayListDTO, $result);
    }

    public function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    public function resolveAction(): GetPaymentGatewaysAction
    {
        return resolve(GetPaymentGatewaysAction::class);
    }
}
