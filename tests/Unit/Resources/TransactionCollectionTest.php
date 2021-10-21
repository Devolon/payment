<?php

namespace Tests\Unit\Admin\Transaction\Resources;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Resources\TransactionCollection;
use Devolon\Payment\Resources\TransactionResource;
use Devolon\Payment\Tests\PaymentTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionCollectionTest extends PaymentTestCase
{
    use WithFaker;

    public function testCollectionWithPaginator()
    {
        // Arrange
        $transaction = Transaction::factory()->create();

        $transactionPaginator = new LengthAwarePaginator(
            [$transaction],
            1,
            1,
            1
        );

        $expectedTransactionList = [TransactionResource::make($transaction)->response()->getData(true)];

        // Act
        $result = TransactionCollection::make($transactionPaginator)->response()->getData(true);

        // Assert
        $this->assertArrayHasKey('data', $result);
        $this->assertEquals($expectedTransactionList, $result['data']);
    }
}
