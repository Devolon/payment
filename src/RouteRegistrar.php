<?php

namespace Devolon\Payment;

use Devolon\Common\Middleware\Pagination;
use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    public function __construct(private Router $router)
    {
    }

    public function transactionRoutes(): void
    {
        $this->router->post('payment/transaction', [
            'uses' => 'Controllers\CreateTransactionController',
            'as' => 'payment.transaction.create',
        ]);
        $this->router->patch('payment/transaction/{transaction}', [
            'uses' => 'Controllers\UpdateTransactionController',
            'as' => 'payment.transaction.update',
        ]);
        $this->router->get('payment/transaction', [
            'uses' => 'Controllers\GetUserTransactionController',
            'as' => 'payment.transaction.index',
        ])->middleware(Pagination::class);
        $this->router->get('/payment/gateway', [
            'uses' => 'Controllers\GetPaymentGatewaysController',
            'as' => 'payment.gateway.index',
        ]);
    }

    public function callbackRoutes(): void
    {
        $this->router->any('/payment/callback/{transaction}/{status}', [
            'uses' => 'Controllers\HandlePaymentCallbackController',
            'as' => 'payment.callback',
        ]);
    }
}
