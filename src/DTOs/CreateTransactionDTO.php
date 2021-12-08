<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;

class CreateTransactionDTO extends DTO
{
    public function __construct(
        public int $user_id,
        public string $payment_method,
        public array $payment_method_data,
        public string $product_type,
        public array $product_data,
    ) {
    }

    public static function fromArray(array $array): static
    {
        return parent::fromArray(
            array_merge(
                $array,
                [
                    'payment_method_data' => $array['payment_method_data'] ?? [],
                    'product_data' => $array['product_data'] ?? [],
                ]
            )
        );
    }
}
