<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\CanRefund;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;

class PaymentGatewayDiscoveryServiceTest extends PaymentTestCase
{
    use WithFaker;

    /**
     * @dataProvider provideData
     */
    public function testGetReturnsProperGateway($gateways, $requestedGatewayName, $expectedGateway)
    {
        // Arrange
        $service = new PaymentGatewayDiscoveryService($gateways);

        // Act
        $result = $service->get($requestedGatewayName);

        // Assert
        $this->assertInstanceOf(PaymentGatewayInterface::class, $result);
        $this->assertEquals($expectedGateway, $result);
    }

    public function testGetThrowsInvalidArgumentExceptionWithWrongGatewayName(): void
    {
        // Arrange
        $service = new PaymentGatewayDiscoveryService([]);

        // Expect
        $this->expectException(InvalidArgumentException::class);

        // Act
        $service->get('wrong_name');
    }

    public function testGetAllNamesReturnsNameOfAllPaymentGateways(): void
    {
        // Arrange
        $firstGatewayName = $this->faker->word;
        $firstGateway = $this->mockPaymentGateway();
        $secondGatewayName = $this->faker->word;
        $secondGateway = $this->mockPaymentGateway();
        $gateways = [
            $firstGatewayName => $firstGateway,
            $secondGatewayName => $secondGateway,
        ];
        $service = new PaymentGatewayDiscoveryService($gateways);

        // Act
        $result = $service->getAllNames();

        // Assert
        $this->assertEquals([$firstGatewayName, $secondGatewayName], $result);
    }

    public function testGetGatewaysWithRefundReturnsNameOfAllPaymentGatewaysWhichCanRefund(): void
    {
        // Arrange
        $firstGatewayName = $this->faker->word;
        $firstGateway = $this->mockPaymentGateway();
        $secondGatewayName = $this->faker->word;
        $secondGateway = $this->mockPaymentGateway(CanRefund::class);
        $gateways = [
            $firstGatewayName => $firstGateway,
            $secondGatewayName => $secondGateway,
        ];
        $service = new PaymentGatewayDiscoveryService($gateways);

        // Act
        $result = $service->getGatewaysWithRefund();

        // Assert
        $this->assertEquals([$secondGatewayName], $result);
    }

    public function provideData(): array
    {
        $faker = $this->makeFaker('en_US');
        $firstGatewayName = $faker->word;
        $firstGateway = $this->mockPaymentGateway();
        $secondGatewayName = $faker->word;
        $secondGateway = $this->mockPaymentGateway();
        $gateways = [
            $firstGatewayName => $firstGateway,
            $secondGatewayName => $secondGateway,
        ];

        return [
            'get first gateway' => [$gateways, $firstGatewayName, $firstGateway],
            'get second gateway' => [$gateways, $secondGatewayName, $secondGateway],
        ];
    }

    private function mockPaymentGateway(string ...$interfaces): MockInterface | PaymentGatewayInterface
    {
        return Mockery::mock(PaymentGatewayInterface::class, ...$interfaces);
    }
}
