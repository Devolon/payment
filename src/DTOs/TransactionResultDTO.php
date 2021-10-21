<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;
use Devolon\Payment\Models\Transaction;

class TransactionResultDTO extends DTO
{
    public function __construct(
        public Transaction $transaction,
        public bool $should_redirect,
        public ?RedirectDTO $redirect_to,
    ) {
    }
}
