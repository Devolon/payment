<?php

namespace Devolon\Payment\Tests\Unit\Services;

use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\Exceptions\TransactionNotSupported;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Services\CreateTransactionService;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Tests\Stubs\OnlyDummyGatewayProductType;
use Devolon\Payment\Tests\Stubs\SampleProductType;
use Illuminate\Foundation\Testing\WithFaker;

class CreateTransactionServiceTest extends PaymentTestCase
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
    public function testInvokeWithDeprecatedProductTypes()
    {
        // Arrange
        $price = 123.45;
        $user = $this->getUserClass()::factory()->create();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            'user_id' => $user->id,
            'payment_method' => $this->faker->word,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
            'product_type' => StubProduct::class,
        ]);
        Payment::addProductType(StubProduct::class);

        $sut = $this->resolveService();

        // Act
        $result = $sut($createTransactionDTO);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_IN_PROCESS,
            'payment_method' => $createTransactionDTO->payment_method,
            'money_amount' => $price,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($createTransactionDTO->payment_method_data, $result->payment_method_data);
    }

    /**
     * @deprecated
     */
    public function testInvoke()
    {
        // Arrange
        $price = 10.5;
        $user = $this->getUserClass()::factory()->create();
        $productType = $this->resolveSampleProductType();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            'user_id' => $user->id,
            'payment_method' => $this->faker->word,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
            'product_type' => $productType->getName(),
            'product_data' => [
                $this->faker->word => $this->faker->word,
            ],
        ]);

        $sut = $this->resolveService();

        // Act
        $result = $sut($createTransactionDTO);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_IN_PROCESS,
            'payment_method' => $createTransactionDTO->payment_method,
            'money_amount' => $price,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($createTransactionDTO->payment_method_data, $result->payment_method_data);
        $this->assertEquals($createTransactionDTO->product_data, $result->product_data);
    }

    /**
     * @deprecated
     */
    public function testInvokeSuccessfulForOnlyDummyGatewayProductType()
    {
        // Arrange
        $price = 10.5;
        $user = $this->getUserClass()::factory()->create();
        $productType = $this->resolveOnlyDummyGatewayProductType();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            'user_id' => $user->id,
            'payment_method' => 'dummy',
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
            'product_type' => $productType->getName(),
        ]);

        $sut = $this->resolveService();

        // Act
        $result = $sut($createTransactionDTO);

        // Assert
        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertDatabaseHas('transactions', [
            'id' => $result->id,
            'status' => Transaction::STATUS_IN_PROCESS,
            'payment_method' => $createTransactionDTO->payment_method,
            'money_amount' => $price,
            'user_id' => $user->id,
        ]);
        $this->assertEquals($createTransactionDTO->payment_method_data, $result->payment_method_data);
    }

    /**
     * @deprecated
     */
    public function testInvokeFailForOnlyDummyGatewayProductType()
    {
        // Arrange
        $price = 10.5;
        $user = $this->getUserClass()::factory()->create();
        $productType = $this->resolveOnlyDummyGatewayProductType();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            'user_id' => $user->id,
            'payment_method' => $this->faker->word,
            'payment_method_data' => [
                $this->faker->word => $this->faker->word,
            ],
            'product_type' => $productType->getName(),
        ]);

        $sut = $this->resolveService();

        // Expect
        $this->expectException(TransactionNotSupported::class);

        // Act
        $sut($createTransactionDTO);

        // Assert
        $this->assertDatabaseMissing('transactions', [
            'status' => Transaction::STATUS_IN_PROCESS,
            'payment_method' => $createTransactionDTO->payment_method,
            'money_amount' => $price,
            'user_id' => $user->id,
        ]);
    }

    private function resolveService(): CreateTransactionService
    {
        return resolve(CreateTransactionService::class);
    }

    private function resolveSampleProductType(): SampleProductType
    {
        $this->app->tag(SampleProductType::class, ProductTypeInterface::class);

        return resolve(SampleProductType::class);
    }

    private function resolveOnlyDummyGatewayProductType(): OnlyDummyGatewayProductType
    {
        $this->app->tag(OnlyDummyGatewayProductType::class, ProductTypeInterface::class);

        return resolve(OnlyDummyGatewayProductType::class);
    }
}
