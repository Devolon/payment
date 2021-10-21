<?php

namespace Devolon\Payment\Tests\Unit\Resources;

use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\DTOs\TransactionResultDTO;
use Devolon\Payment\Resources\TransactionResultResource;
use Devolon\Payment\Resources\TransactionResource;
use Devolon\Payment\Resources\RedirectDTOResource;
use Illuminate\Foundation\Testing\WithFaker;

class TransactionResultResourceTest extends PaymentTestCase
{
    use WithFaker;

    /**
     * @group app
     * @group transaction
     * @group success
     */
    public function testMakeResourceSuccessfully()
    {
        // Arrange
        $transaction = Transaction::factory()->create();
        $redirectDTO = RedirectDTO::fromArray([
            'redirect_url' => $this->faker->url,
            'redirect_method' => $this->faker->word,
            'redirect_data' => [
                $this->faker->word => $this->faker->word,
            ],
        ]);
        $transactionResultDTO = TransactionResultDTO::fromArray([
            'transaction' => $transaction,
            'should_redirect' => true,
            'redirect_to' => $redirectDTO,
        ]);
        $expectedResult = [
            'transaction' => TransactionResource::make($transaction)->response()->getData(true),
            'should_redirect' => true,
            'redirect_to' => RedirectDTOResource::make($redirectDTO)->response()->getData(true),
        ];

        // Act
        $result = TransactionResultResource::make($transactionResultDTO)->response()->getData(true);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @group app
     * @group transaction
     * @group success
     */
    public function testMakeResourceSuccessfullyWithoutRedirect()
    {
        // Arrange
        $transaction = Transaction::factory()->create();
        $redirectDTO = RedirectDTO::fromArray([
            'redirect_url' => $this->faker->url,
            'redirect_method' => $this->faker->word,
            'redirect_data' => [
                $this->faker->word => $this->faker->word,
            ],
        ]);
        $transactionResultDTO = TransactionResultDTO::fromArray([
            'transaction' => $transaction,
            'should_redirect' => false,
            'redirect_to' => $redirectDTO,
        ]);
        $expectedResult = [
            'transaction' => TransactionResource::make($transaction)->response()->getData(true),
            'should_redirect' => false,
            'redirect_to' => null,
        ];

        // Act
        $result = TransactionResultResource::make($transactionResultDTO)->response()->getData(true);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }
}
