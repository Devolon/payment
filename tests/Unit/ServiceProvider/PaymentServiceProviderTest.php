<?php

namespace Devolon\Payment\Tests\Unit\ServiceProvider;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\PurchaseResultDTO;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;

class PaymentServiceProviderTest extends PaymentTestCase
{
    public function testItSendsAnyTaggedSMSServiceToSMSSenderResolver()
    {
        // Arrange
        $fakePaymentGateway = $this->resolveAndTagFakePaymentGateway();
        $paymentGatewayDiscoveryService = $this->resolvePaymentGatewayDiscoveryService();

        // Act
        $result = $paymentGatewayDiscoveryService->getAllNames();

        // Assert
        $this->assertEquals([$fakePaymentGateway->getName()], $result);
    }

    private function resolvePaymentGatewayDiscoveryService(): PaymentGatewayDiscoveryService
    {
        return resolve(PaymentGatewayDiscoveryService::class);
    }

    private function resolveAndTagFakePaymentGateway(): PaymentGatewayInterface
    {
        $class = get_class(new class () implements PaymentGatewayInterface {
            private ?string $name = null;
            public function purchase(Transaction $transaction): PurchaseResultDTO
            {
                return PurchaseResultDTO::fromArray([
                    'redirect_url' => 'https://example.com/pay',
                    'redirect_method' => 'POST',
                    'redirect_data' => [
                        'key' => 'value',
                    ],
                ]);
            }

            public function getName(): string
            {
                if ($this->name) {
                    return $this->name;
                }

                $this->name = uniqid();

                return $this->name;
            }

            public function verify(Transaction $transaction, array $data): bool
            {
                return true;
            }
        });
        $this->app->singleton($class);
        $this->app->tag($class, PaymentGatewayInterface::class);

        return resolve($class);
    }
}
