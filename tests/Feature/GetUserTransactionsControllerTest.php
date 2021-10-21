<?php

namespace Devolon\Payment\Tests\Feature;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Actions\GetUserTransactionListAction;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Resources\TransactionCollection;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;

class GetUserTransactionsControllerTest extends PaymentTestCase
{
    private const ROUTE_NAME = 'app.payment.transaction.index';

    /**
     * @group app
     * @group transaction
     * @group route
     * @group success
     */
    public function testRoute()
    {
        // Act
        $result = route(self::ROUTE_NAME, [], false);

        // Assert
        $this->assertEquals("/payment/transaction", $result);
    }

    /**
     * @group app
     * @group ticket-instance
     * @group success
     */
    public function testSuccess()
    {
        // Arrange
        $user = $this->getUserClass()::factory()->create();
        $transaction = Transaction::factory()->inProcess()->create(['payment_method' => 'dummy']);
        $perPage = Setting::PAGE_SIZE;
        $mockedPaginatedList = new LengthAwarePaginator(
            [$transaction],
            10,
            $perPage,
            1,
        );

        $getUserTicketInstanceListAction = $this->mockGetUserTransactionListAction();

        // Expect
        $getUserTicketInstanceListAction
            ->shouldReceive('__invoke')
            ->withArgs([$user->id, $perPage])
            ->once()
            ->andReturn($mockedPaginatedList);

        // Act
        $response = $this->actingAs($user)->getJson(route(self::ROUTE_NAME));

        // Assert
        $response
            ->assertOk()
            ->assertJson(
                TransactionCollection::make($mockedPaginatedList)
                    ->response()
                    ->getData(true)
            );
    }

    private function mockGetUserTransactionListAction(): MockInterface
    {
        return $this->mock(GetUserTransactionListAction::class);
    }
}
