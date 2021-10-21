<?php

namespace Devolon\Payment\Contracts;

use Devolon\Payment\DTOs\CreateTransactionDTO;

interface HasPrice
{
    public static function calculatePrice(CreateTransactionDTO $createTransactionDTO): float;
}
