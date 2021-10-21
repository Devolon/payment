<?php

namespace Devolon\Payment\Contracts;

interface HasPurchaseData
{
    public function purchaseDataRules(): array;
}
