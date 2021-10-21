<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Services\GenerateCallbackURLService;
use Illuminate\Foundation\Testing\WithFaker;

class GenerateCallbackURLServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $service = $this->resolveService();
        $transaction = Transaction::factory()->create();
        $status = $this->faker->word;
        $baseUrl = "https://{$this->faker->domainName}";
        config([
            'payment_highway.app_base_url' => $baseUrl,
        ]);
        $expectedPath = route('app.payment.callback', ['transaction' => $transaction->id, 'status' => $status], false);
        $expectedAddress = "{$baseUrl}{$expectedPath}";

        // Act
        $result = $service($transaction, $status);

        // Assert
        $this->assertEquals($expectedAddress, $result);
    }

    public function resolveService(): GenerateCallbackURLService
    {
        return resolve(GenerateCallbackURLService::class);
    }
}
