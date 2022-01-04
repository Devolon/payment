<?php

namespace Devolon\Payment\Tests\Unit\Policies;

use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Exceptions\TransactionNotSupported;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Policies\TransactionPolicy;
use Devolon\Payment\Services\CreateTransactionService;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Tests\Stubs\OnlyDummyGatewayProductType;
use Devolon\Payment\Tests\Stubs\SampleProductType;
use Devolon\Payment\Tests\User;
use Illuminate\Foundation\Testing\WithFaker;

class TransactionPolicyTest extends PaymentTestCase
{
    use WithFaker;

    protected function tearDown(): void
    {
        Payment::clearProductTypes();
        parent::tearDown();
    }

    /**
     * @deprecated
     */
    public function testRefundTrue()
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'status' => Transaction::STATUS_DONE,
        ]);
        $policy = resolve(TransactionPolicy::class);
        $user = $this->getUserClass()::factory()->create();

        // Act
        $result = $policy->refund($user, $transaction);

        // Assert
        $this->assertTrue($result);
    }

    /**
     * @deprecated
     */
    public function testRefundFalse()
    {
        // Arrange
        $transaction = Transaction::factory()->create([
            'status' => collect([
                    Transaction::STATUS_FAILED, Transaction::STATUS_IN_PROCESS, Transaction::STATUS_REFUNDED
                ])
                ->random(),
        ]);
        $policy = resolve(TransactionPolicy::class);
        $user = $this->getUserClass()::factory()->create();

        // Act
        $result = $policy->refund($user, $transaction);

        // Assert
        $this->assertFalse($result);
    }

}
