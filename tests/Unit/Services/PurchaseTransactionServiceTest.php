<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Services\PurchaseTransactionService;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class PurchaseTransactionServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $paymentMethod = $this->faker->word;
        $transaction = Transaction::factory()->inProcess()->create([
            'payment_method' => $paymentMethod,
        ]);
        $purchaseResultDTO = PurchaseResultDTO::fromArray([
            'should_redirect' => true,
            'redirect_to' => RedirectDTO::fromArray([
                'redirect_url' => $this->faker->url,
                'redirect_method' => $this->faker->word,
                'redirect_data' => [
                    $this->faker->word => $this->faker->word,
                ],
            ])
        ]);

        $paymentGatewayDiscoveryService = $this->mockPaymentGatewayDiscoveryService();
        $paymentGateway = $this->mockPaymentGateway();
        $service = $this->resolveService();

        // Expect
        $paymentGatewayDiscoveryService
            ->shouldReceive('get')
            ->with($paymentMethod)
            ->once()
            ->andReturn($paymentGateway);

        $paymentGateway
            ->shouldReceive('purchase')
            ->with($transaction)
            ->once()
            ->andReturn($purchaseResultDTO);

        // Act
        $result = $service($transaction);

        // Assert
        $this->assertEquals($purchaseResultDTO, $result);
    }

    private function resolveService(): PurchaseTransactionService
    {
        return resolve(PurchaseTransactionService::class);
    }

    private function mockPaymentGatewayDiscoveryService(): MockInterface
    {
        return $this->mock(PaymentGatewayDiscoveryService::class);
    }

    private function mockPaymentGateway(): MockInterface
    {
        return $this->mock(PaymentGatewayInterface::class);
    }
}
