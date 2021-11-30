<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\GetTransactionsService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class GetTransactionsServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $service = $this->resolveService();
        $count = $this->faker->numberBetween(2, 10);
        Transaction::factory()
            ->count($count)
            ->create();

        // Act
        $result = $service($count);

        // Assert
        $this->assertEquals(
            Transaction::query()
                ->count(),
            $result->total()
        );
        $this->assertContainsOnlyInstancesOf(Transaction::class, $result);
        $this->assertTrue($result->items()[0]->created_at > $result->items()[$count - 1]->created_at);
        $this->assertEquals($count, $result->perPage());
    }

    private function resolveService(): GetTransactionsService
    {
        return resolve(GetTransactionsService::class);
    }
}
