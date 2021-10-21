<?php

namespace Devolon\Payment\Contracts;

interface HasUpdateTransactionData
{
    public function updateTransactionDataRules(string $newStatus): array;
}
