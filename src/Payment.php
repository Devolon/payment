<?php

namespace Devolon\Payment;

use Illuminate\Support\Facades\Route;

class Payment
{
    /**
     * @var array<string>
     */
    private static array $productTypes = [];

    public static function transactionRoutes(array $options = [])
    {
        $defaultOptions = [
            'namespace' => '\Devolon\Payment',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) {
            (new RouteRegistrar($router))->transactionRoutes();
        });
    }

    public static function callbackRoutes(array $options = [])
    {
        $defaultOptions = [
            'namespace' => '\Devolon\Payment',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) {
            (new RouteRegistrar($router))->callbackRoutes();
        });
    }

    // todo It should be better if we remove this part from this class

    public static function addProductType(string $productTypeClass): void
    {
        $morphName = defined("$productTypeClass::MORPH_NAME") ?
            $productTypeClass::MORPH_NAME : $productTypeClass;

        if (!in_array($morphName, array_keys(self::$productTypes))) {
            self::$productTypes[$morphName] = $productTypeClass;
        }
    }

    /**
     * @return array<string>
     */
    public static function getProductTypes(): array
    {
        return array_keys(self::$productTypes);
    }

    public static function getProductClass($productType): string
    {
        return self::$productTypes[$productType];
    }

    public static function clearProductTypes(): void
    {
        self::$productTypes = [];
    }
}
