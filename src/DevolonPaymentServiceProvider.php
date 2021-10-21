<?php

namespace Devolon\Payment;

use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Services\PaymentGatewayDiscoveryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class DevolonPaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaymentGatewayDiscoveryService::class, function (Application $application) {
            /** @var PaymentGatewayInterface[] $gateways */
            $paymentGateways = $application->tagged(PaymentGatewayInterface::class);

            $gatewaysMap = [];
            foreach ($paymentGateways as $gateway) {
                $gatewaysMap[$gateway->getName()] = $gateway;
            }

            return new PaymentGatewayDiscoveryService($gatewaysMap);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Virtual/' => app_path('Virtuals/Payment')
        ], 'devolon-payment-swagger-documentation');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'devolon-payment-migrations');

        $this->registerMigrations();
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
}
