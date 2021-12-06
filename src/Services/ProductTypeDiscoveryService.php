<?php

namespace Devolon\Payment\Services;

use Devolon\Payment\Contracts\ProductTypeInterface;
use InvalidArgumentException;

class ProductTypeDiscoveryService
{
    /**
     * @param array<string, ProductTypeInterface> $productTypes
     */
    public function __construct(private array $productTypes)
    {
    }

    public function get(string $productTypeName): ProductTypeInterface
    {
        if (!isset($this->productTypes[$productTypeName])) {
            throw new InvalidArgumentException('Product type name is wrong');
        }

        return $this->productTypes[$productTypeName];
    }

    /**
     * @return array<string>
     */
    public function getAllNames(): array
    {
        return array_keys($this->productTypes);
    }
}