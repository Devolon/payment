<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;

class UpdateTransactionDTO extends DTO
{
    public function __construct(
        public string $status,
        public array $payment_method_data = [],
    ) {
    }
}
