<?php

namespace Devolon\Payment\Tests\Unit\Actions;

use Devolon\Payment\Actions\CreateTransactionAction;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\DTOs\PurchaseResultDTO;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\DTOs\TransactionResultDTO;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Services\CreateTransactionService;
use Devolon\Payment\Services\MakeTransactionDoneService;
use Devolon\Payment\Services\PurchaseTransactionService;
use Devolon\Payment\Tests\PaymentTestCase;
use Hamcrest\Core\IsEqual;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\MockInterface;

class CreateTransactionActionTest extends PaymentTestCase
{
    use WithFaker;

    public function testInvoke(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            "user_id" => $transaction->user_id,
            "payment_method" => $this->faker->word,
            "payment_method_data" => [],
            "product_type" => $this->faker->word,
        ]);
        $purchaseResultDTO = new PurchaseResultDTO(
            should_redirect: true,
            redirect_to: new RedirectDTO(
                redirect_url: $this->faker->url,
                redirect_method: $this->faker->randomElement(['POST', 'GET']),
                redirect_data: [
                    $this->faker->word => $this->faker->word,
                ],
            ),
        );
        $transactionResultDTO = new TransactionResultDTO(
            transaction: $transaction,
            should_redirect: $purchaseResultDTO->should_redirect,
            redirect_to: $purchaseResultDTO->redirect_to,
        );

        $createTransactionService = $this->mockCreateTransactionService();
        $purchaseTransactionService = $this->mockPurchaseTransactionService();

        $action = $this->resolveAction();

        // Expect
        $createTransactionService
            ->shouldReceive('__invoke')
            ->with(IsEqual::equalTo($createTransactionDTO))
            ->once()
            ->andReturn($transaction);

        $purchaseTransactionService
            ->shouldReceive('__invoke')
            ->with($transaction)
            ->once()
            ->andReturn($purchaseResultDTO);

        // Act
        $result = $action($createTransactionDTO);

        // Assert
        $this->assertEquals($transactionResultDTO, $result);
    }

    public function testInvokeWhileNoRedirectIsRequired(): void
    {
        // Arrange
        $transaction = Transaction::factory()->create();
        $mockedDoneTransaction = Transaction::factory()->create();
        $createTransactionDTO = CreateTransactionDTO::fromArray([
            "user_id" => $transaction->user_id,
            "payment_method" => $this->faker->word,
            "payment_method_data" => [],
            "product_type" => $this->faker->word,
        ]);
        $purchaseResultDTO = new PurchaseResultDTO(
            should_redirect: false,
            redirect_to: null,
        );
        $transactionResultDTO = new TransactionResultDTO(
            transaction: $mockedDoneTransaction,
            should_redirect: $purchaseResultDTO->should_redirect,
            redirect_to: $purchaseResultDTO->redirect_to,
        );

        $mockedCreateTransactionService = $this->mockCreateTransactionService();
        $mockedPurchaseTransactionService = $this->mockPurchaseTransactionService();
        $mockedMakeTransactionDoneService = $this->mockMakeTransactionDoneService();

        $sut = $this->resolveAction();

        // Expect
        $mockedCreateTransactionService
            ->shouldReceive('__invoke')
            ->with(IsEqual::equalTo($createTransactionDTO))
            ->once()
            ->andReturn($transaction);

        $mockedPurchaseTransactionService
            ->shouldReceive('__invoke')
            ->with($transaction)
            ->once()
            ->andReturn($purchaseResultDTO);

        $mockedMakeTransactionDoneService
            ->shouldReceive('__invoke')
            ->withArgs([$transaction, $createTransactionDTO->payment_method_data])
            ->once()
            ->andReturn($mockedDoneTransaction);

        // Act
        $result = $sut($createTransactionDTO);

        // Assert
        $this->assertEquals($transactionResultDTO, $result);
    }

    private function mockCreateTransactionService(): MockInterface
    {
        return $this->mock(CreateTransactionService::class);
    }

    private function mockMakeTransactionDoneService(): MockInterface
    {
        return $this->mock(MakeTransactionDoneService::class);
    }

    private function mockPurchaseTransactionService(): MockInterface
    {
        return $this->mock(PurchaseTransactionService::class);
    }

    private function resolveAction(): CreateTransactionAction
    {
        return resolve(CreateTransactionAction::class);
    }
}
