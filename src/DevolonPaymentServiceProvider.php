<?php

namespace Devolon\Payment;

use Devolon\Common\Bases\Repository;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Policies\TransactionPolicy;
use Devolon\Payment\Repositories\TransactionRepository;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Devolon\Payment\Services\ProductTypeDiscoveryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;


class DevolonPaymentServiceProvider extends ServiceProvider
{

    protected $policies = [
        Transaction::class => TransactionPolicy::class,
    ];

    public function register()
    {
        $this->app->tag(TransactionRepository::class, Repository::class);

        $this->app->singleton(PaymentGatewayDiscoveryService::class, function (Application $application) {
            /** @var PaymentGatewayInterface[] $gateways */
            $paymentGateways = $application->tagged(PaymentGatewayInterface::class);

            $gatewaysMap = [];
            foreach ($paymentGateways as $gateway) {
                $gatewaysMap[$gateway->getName()] = $gateway;
            }

            return new PaymentGatewayDiscoveryService($gatewaysMap);
        });

        $this->app->singleton(ProductTypeDiscoveryService::class, function (Application $application) {
            /** @var ProductTypeInterface[] $productTypes */
            $productTypes = $application->tagged(ProductTypeInterface::class);

            $productTypesMap = [];
            foreach ($productTypes as $productType) {
                $productTypesMap[$productType->getName()] = $productType;
            }

            return new ProductTypeDiscoveryService($productTypesMap);
        });
    }

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/payment.php', 'payment');
        $this->publishes([
            __DIR__ . '/../config/payment.php' => config_path('payment.php')
        ], 'payment-highway-config');

        $this->publishes([
            __DIR__ . '/Virtual/' => app_path('Virtuals/Payment')
        ], 'devolon-payment-swagger-documentation');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'devolon-payment-migrations');

        $this->registerMigrations();
        $this->registerPolicies();
    }

    /**
     * Register Passport's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }
}
