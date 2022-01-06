<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\GetUserTransactionsService;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class GetUserTransactionsServiceTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke()
    {
        // Arrange
        $service = $this->resolveService();
        $user = $this->getUserClass()::factory()->create();
        $ownedCount = $this->faker->numberBetween(2, 10);
        Transaction::factory()
            ->count($ownedCount)
            ->create([
                'user_id' => $user->id,
                'status' => Transaction::STATUS_DONE
            ]);
        Transaction::factory()
            ->count($ownedCount)
            ->create([ 'user_id' => $user->id, ]);
        Transaction::factory()
            ->count($ownedCount)
            ->create();

        // Act
        $result = $service($user->id, $ownedCount);

        // Assert
        $this->assertEquals(
            Transaction::query()
                ->where('user_id', $user->id)
                ->where('status', Transaction::STATUS_DONE)
                ->count(),
            $result->total()
        );
        $this->assertContainsOnlyInstancesOf(Transaction::class, $result);
        $this->assertTrue($result->items()[0]->created_at > $result->items()[$ownedCount - 1]->created_at);
        $this->assertEquals($ownedCount, $result->perPage());
    }

    public function testInvokeWithStatus()
    {
        // Arrange
        $service = $this->resolveService();
        $user = $this->getUserClass()::factory()->create();
        $ownedCount = $this->faker->numberBetween(2, 10);
        $status = collect(Transaction::STATUSES)->random();
        Transaction::factory()
            ->count($ownedCount)
            ->create([
                'user_id' => $user->id,
                'status' => $status,
            ]);
        Transaction::factory()
            ->count($ownedCount)
            ->create([ 'user_id' => $user->id, ]);
        Transaction::factory()
            ->count($ownedCount)
            ->create();

        // Act
        $result = $service($user->id, $ownedCount, [$status]);

        // Assert
        $this->assertEquals(
            Transaction::query()
                ->where('user_id', $user->id)
                ->where('status', $status)
                ->count(),
            $result->total()
        );
        $this->assertContainsOnlyInstancesOf(Transaction::class, $result);
        $this->assertTrue($result->items()[0]->created_at > $result->items()[$ownedCount - 1]->created_at);
        $this->assertEquals($ownedCount, $result->perPage());
    }

    public function testInvokeWithStatuses()
    {
        // Arrange
        $service = $this->resolveService();
        $user = $this->getUserClass()::factory()->create();
        $ownedCount = $this->faker->numberBetween(2, 10);
        $statuses = collect(Transaction::STATUSES)->random($this->faker->numberBetween(2, 4))->toArray();

        foreach($statuses as $status) {
            Transaction::factory()
                ->count($ownedCount)
                ->create([
                    'user_id' => $user->id,
                    'status' => $status,
                ]);
        }

        Transaction::factory()
            ->count($ownedCount)
            ->create([ 'user_id' => $user->id, ]);
        Transaction::factory()
            ->count($ownedCount)
            ->create();

        // Act
        $result = $service($user->id, $ownedCount, $statuses);

        // Assert
        $this->assertEquals(
            Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('status', $statuses)
                ->count(),
            $result->total()
        );
        $this->assertContainsOnlyInstancesOf(Transaction::class, $result);
        $this->assertTrue($result->items()[0]->created_at > $result->items()[$ownedCount - 1]->created_at);
        $this->assertEquals($ownedCount, $result->perPage());
    }

    private function resolveService(): GetUserTransactionsService
    {
        return resolve(GetUserTransactionsService::class);
    }
}
