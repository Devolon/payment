<?php

namespace Devolon\Payment\Tests\Feature\Administration;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Actions\Administration\GetTransactionListAction;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Resources\TransactionCollection;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;

class GetTransactionsControllerTest extends PaymentTestCase
{
    private const ROUTE_NAME = 'admin.payment.transaction.index';

    public function testRoute()
    {
        // Act
        $result = route(self::ROUTE_NAME, [], false);

        // Assert
        $this->assertEquals("/admin/payment/transaction", $result);
    }

    public function testSuccess()
    {
        // Arrange
        $transaction = Transaction::factory()->create(['payment_method' => 'dummy']);
        $perPage = Setting::PAGE_SIZE;
        $mockedPaginatedList = new LengthAwarePaginator(
            [$transaction],
            10,
            $perPage,
            1,
        );

        $getUserTicketInstanceListAction = $this->mockGetTransactionListAction();

        // Expect
        $getUserTicketInstanceListAction
            ->shouldReceive('__invoke')
            ->withArgs([$perPage])
            ->once()
            ->andReturn($mockedPaginatedList);

        // Act
        $response = $this->getJson(route(self::ROUTE_NAME));

        // Assert
        $response
            ->assertOk()
            ->assertJson(
                TransactionCollection::make($mockedPaginatedList)
                    ->response()
                    ->getData(true)
            );
    }

    private function mockGetTransactionListAction(): MockInterface
    {
        return $this->mock(GetTransactionListAction::class);
    }
}
