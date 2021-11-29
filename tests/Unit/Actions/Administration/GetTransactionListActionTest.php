<?php

namespace Devolon\Payment\Tests\Unit\Actions\Administration;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Actions\Administration\GetTransactionListAction;
use Devolon\Payment\Services\GetTransactionsService;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;

class GetTransactionListActionTest extends PaymentTestCase
{
    use WithFaker;

    /**
     * @dataProvider invokeDataProvider
     */
    public function testInvoke(int $pageSize)
    {
        // Arrange
        $getTransactionsService = $this->mockGetTransactionsService();
        $action = $this->resolveAction();
        $transactionPaginator = new LengthAwarePaginator(
            Transaction::factory()->count(1)->make(),
            1,
            $pageSize,
            1
        );

        // Expect
        $getTransactionsService
            ->shouldReceive('__invoke')
            ->withArgs([$pageSize])
            ->once()
            ->andReturn($transactionPaginator);

        // Act
        $result = $action($pageSize);

        // Assert
        $this->assertEquals($transactionPaginator, $result);
    }

    public function invokeDataProvider(): array
    {
        $faker = $this->makeFaker('en_US');

        return [
            'default page size' => [
                Setting::PAGE_SIZE,
            ],
            'non default page size' => [
                $faker->numberBetween(10, 100),
            ],
        ];
    }

    private function resolveAction(): GetTransactionListAction
    {
        return resolve(GetTransactionListAction::class);
    }

    private function mockGetTransactionsService(): MockInterface
    {
        return $this->mock(GetTransactionsService::class);
    }
}
