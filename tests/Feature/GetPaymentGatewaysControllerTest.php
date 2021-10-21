<?php

namespace Devolon\Payment\Tests\Feature;

use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Actions\GetPaymentGatewaysAction;
use Devolon\Payment\DTOs\PaymentGatewayListDTO;
use Devolon\Payment\Resources\PaymentGatewayListResource;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class GetPaymentGatewaysControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'app.payment.gateway.index';

    /**
     * @group app
     * @group gateway
     * @group route
     * @group success
     */
    public function testRoute()
    {
        // Act
        $result = route(self::ROUTE_NAME, [], false);

        // Assert
        $this->assertEquals('/payment/gateway', $result);
    }

    public function testSuccess()
    {
        // Arrange
        $getPaymentGatewaysAction = $this->mockGetPaymentGatewaysAction();
        $nameRange = range('A', 'Z');
        $paymentGatewayListDTO = PaymentGatewayListDTO::fromArray([
            'payment_gateways' => $this->faker->randomElements(
                $nameRange,
                $this->faker->numberBetween(1, count($nameRange))
            )
        ]);

        // Expect
        $getPaymentGatewaysAction
            ->shouldReceive('__invoke')
            ->withNoArgs()
            ->once()
            ->andReturn($paymentGatewayListDTO)
        ;

        // Act
        $response = $this->getJson(route(self::ROUTE_NAME));

        // Assert
        $response
            ->assertSuccessful()
            ->assertJson(PaymentGatewayListResource::make($paymentGatewayListDTO)->response()->getData(true))
        ;
    }

    private function mockGetPaymentGatewaysAction(): MockInterface
    {
        return $this->mock(GetPaymentGatewaysAction::class);
    }
}
