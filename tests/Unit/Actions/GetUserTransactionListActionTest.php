<?php

namespace Devolon\Payment\Tests\Unit\Actions;

use Devolon\Common\Tools\Setting;
use Devolon\Payment\Actions\GetUserTransactionListAction;
use Devolon\Payment\Services\GetUserTransactionsService;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;

class GetUserTransactionListActionTest extends PaymentTestCase
{
    use WithFaker;

    /**
     * @dataProvider invokeDataProvider
     */
    public function testInvoke(int $pageSize, ?array $statuses = null)
    {
        // Arrange
        $user = $this->getUserClass()::factory()->create();
        $getUserTransactionsService = $this->mockGetUserTransactionsService();
        $action = $this->resolveAction();
        $transactionPaginator = new LengthAwarePaginator(
            Transaction::factory()->count(1)->make(),
            1,
            $pageSize,
            1
        );

        // Expect
        $getUserTransactionsService
            ->shouldReceive('__invoke')
            ->withArgs([$user->id, $pageSize, $statuses])
            ->once()
            ->andReturn($transactionPaginator);

        // Act
        $result = $action($user->id, $pageSize, $statuses);

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
            'with statuses' => [
                Setting::PAGE_SIZE,
                $faker->words(10),
            ],
        ];
    }

    private function resolveAction(): GetUserTransactionListAction
    {
        return resolve(GetUserTransactionListAction::class);
    }

    private function mockGetUserTransactionsService(): MockInterface
    {
        return $this->mock(GetUserTransactionsService::class);
    }
}
