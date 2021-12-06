<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\HasProductData;
use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\Services\GetProductDataRulesService;
use Devolon\Payment\Services\ProductTypeDiscoveryService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;

class GetProductDataRulesServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvokeWhenProductTypeDoesNotImplementHasProductData()
    {
        // Arrange
        $productTypeDiscoveryService = $this->mockProductTypeDiscoveryService();
        $productType = $this->mockProductType();
        $service = $this->resolveService();
        $productTypeName = $this->faker->word;

        // Expect
        $productTypeDiscoveryService
            ->shouldReceive('get')
            ->with($productTypeName)
            ->once()
            ->andReturn($productType);

        $productType->shouldNotReceive('productDataRules');

        // Act
        $result = $service($productTypeName);

        // Assert
        $this->assertEquals([], $result);
    }

    public function testInvokeWhenProductTypeImplementHasProductData()
    {
        // Arrange
        $productType = $this->mockProductTypeAndHasProductData();
        $productTypeDiscoveryService = $this->mockProductTypeDiscoveryService();
        $service = $this->resolveService();
        $productTypeName = $this->faker->word;
        $productDataRules = [
            $this->faker->word => $this->faker->word,
        ];

        // Expect
        $productTypeDiscoveryService
            ->shouldReceive('get')
            ->with($productTypeName)
            ->once()
            ->andReturn($productType);

        $productType
            ->shouldReceive('productDataRules')
            ->withNoArgs()
            ->once()
            ->andReturn($productDataRules);

        // Act
        $result = $service($productTypeName);

        // Assert
        $this->assertEquals($productDataRules, $result);
    }

    private function resolveService()
    {
        return resolve(GetProductDataRulesService::class);
    }

    private function mockProductTypeDiscoveryService(): MockInterface
    {
        return $this->mock(ProductTypeDiscoveryService::class);
    }

    private function mockProductType(): MockInterface
    {
        return Mockery::mock(ProductTypeInterface::class);
    }

    private function mockProductTypeAndHasProductData(): MockInterface
    {
        return Mockery::mock(ProductTypeInterface::class, HasProductData::class);
    }
}
