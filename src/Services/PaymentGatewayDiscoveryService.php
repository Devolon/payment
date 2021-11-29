<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\CanRefund;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use InvalidArgumentException;

class PaymentGatewayDiscoveryService
{
    /**
     * @param array<string, PaymentGatewayInterface> $gateways
     */
    public function __construct(private array $gateways)
    {
    }

    public function get(string $paymentMethod): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$paymentMethod])) {
            throw new InvalidArgumentException('Payment method name is wrong');
        }

        return $this->gateways[$paymentMethod];
    }

    /**
     * @return array<string>
     */
    public function getAllNames(): array
    {
        return array_keys($this->gateways);
    }

    /**
     * @return array<string>
     */
    public function getGatewaysWithRefund(): array
    {
        $gatewaysWithRefund = [];
        foreach ($this->gateways as $name => $gateway) {
            if ($gateway instanceof CanRefund) {
                $gatewaysWithRefund[] = $name;
            }
        }

        return $gatewaysWithRefund;
    }
}
