<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Services\SetGatewayResultService;
use Illuminate\Foundation\Testing\WithFaker;

class SetGatewayResultServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testItSetsGatewayResults()
    {
        // Arrange
        $existingGatewayResultKey = $this->faker->word;
        $existingGatewayResultValue = [
            $this->faker->word => $this->faker->word,
        ];
        $newGatewayResultKey = $this->faker->word;
        $newGatewayResultValue = [
            $this->faker->word => $this->faker->word,
        ];
        $transaction = Transaction::factory()->create(
            [
                'gateway_results' => [
                    $existingGatewayResultKey => $existingGatewayResultValue
                ],
            ]
        );

        $service = $this->resolveService();

        // Act
        $result = $service($transaction, $newGatewayResultKey, $newGatewayResultValue);

        // Assert
        $this->assertArrayHasKey($existingGatewayResultKey, $result['gateway_results']);
        $this->assertEquals($result['gateway_results'][$existingGatewayResultKey], $existingGatewayResultValue);
        $this->assertArrayHasKey($newGatewayResultKey, $result['gateway_results']);
        $this->assertEquals($result['gateway_results'][$newGatewayResultKey], $newGatewayResultValue);
        $transaction->refresh();
        $this->assertEquals([
            $existingGatewayResultKey => $existingGatewayResultValue,
            $newGatewayResultKey => $newGatewayResultValue,
        ], $transaction->gateway_results);
    }

    public function resolveService(): SetGatewayResultService
    {
        return resolve(SetGatewayResultService::class);
    }
}
