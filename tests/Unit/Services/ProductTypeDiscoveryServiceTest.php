<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\CanRefund;
use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\Services\ProductTypeDiscoveryService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;

class ProductTypeDiscoveryServiceTest extends PaymentTestCase
{
    use WithFaker;

    /**
     * @dataProvider provideData
     */
    public function testGetReturnsProperProductType($productTypes, $requestedProductTypeName, $expectedProductType)
    {
        // Arrange
        $service = new ProductTypeDiscoveryService($productTypes);

        // Act
        $result = $service->get($requestedProductTypeName);

        // Assert
        $this->assertInstanceOf(ProductTypeInterface::class, $result);
        $this->assertEquals($expectedProductType, $result);
    }

    public function testGetThrowsInvalidArgumentExceptionWithWrongProductTypeName(): void
    {
        // Arrange
        $service = new ProductTypeDiscoveryService([]);

        // Expect
        $this->expectException(InvalidArgumentException::class);

        // Act
        $service->get('wrong_name');
    }

    public function testGetAllNamesReturnsNameOfAllProductTypes(): void
    {
        // Arrange
        $firstProductTypeName = $this->faker->word;
        $firstProductType = $this->mockProductType();
        $secondProductTypeName = $this->faker->word;
        $secondProductType = $this->mockProductType();
        $productTypes = [
            $firstProductTypeName => $firstProductType,
            $secondProductTypeName => $secondProductType,
        ];
        $service = new ProductTypeDiscoveryService($productTypes);

        // Act
        $result = $service->getAllNames();

        // Assert
        $this->assertEquals([$firstProductTypeName, $secondProductTypeName], $result);
    }

    public function provideData(): array
    {
        $faker = $this->makeFaker('en_US');
        $firstProductTypeName = $faker->word;
        $firstProductType = $this->mockProductType();
        $secondProductTypeName = $faker->word;
        $secondProductType = $this->mockProductType();
        $productTypes = [
            $firstProductTypeName => $firstProductType,
            $secondProductTypeName => $secondProductType,
        ];

        return [
            'get first gateway' => [$productTypes, $firstProductTypeName, $firstProductType],
            'get second gateway' => [$productTypes, $secondProductTypeName, $secondProductType],
        ];
    }

    private function mockProductType(string ...$interfaces): MockInterface
    {
        return Mockery::mock(ProductTypeInterface::class, ...$interfaces);
    }
}
