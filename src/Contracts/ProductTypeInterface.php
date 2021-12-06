<?php

namespace Devolon\Payment\Contracts;

use Devolon\Payment\DTOs\CreateTransactionDTO;

interface ProductTypeInterface
{
    public function getName(): string;
    public function calculatePrice(CreateTransactionDTO $createTransactionDTO): float;
    public function supports(CreateTransactionDTO $createTransactionDTO): bool;
}
