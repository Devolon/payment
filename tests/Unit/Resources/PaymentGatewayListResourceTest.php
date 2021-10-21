<?php

namespace Devolon\Payment\Tests\Unit\Resources;

use Devolon\Payment\DTOs\PaymentGatewayListDTO;
use Devolon\Payment\Resources\PaymentGatewayListResource;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class PaymentGatewayListResourceTest extends PaymentTestCase
{
    use WithFaker;

    public function testResponse()
    {
        // Arrange
        $nameRange = range('A', 'Z');
        $gateways = $this->faker->randomElements(
            $nameRange,
            $this->faker->numberBetween(1, count($nameRange))
        );
        $paymentGatewayListDTO = PaymentGatewayListDTO::fromArray([
            'payment_gateways' => $gateways
        ]);

        $expectedData = [
            'payment_gateways' => $gateways,
        ];

        // Act
        $result = PaymentGatewayListResource::make($paymentGatewayListDTO)->response()->getData(true);

        // Assert
        $this->assertEquals($expectedData, $result);
    }
}
