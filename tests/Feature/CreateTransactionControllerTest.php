<?php

namespace Devolon\Payment\Tests\Feature;

use Devolon\Payment\Contracts\ProductTypeInterface;
use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Gateways\Dummy\DummyGateway;
use Devolon\Payment\Gateways\Dummy\DummyTwoGateway;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\Payment;
use Devolon\Payment\Contracts\PaymentGatewayInterface;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\DTOs\CreateTransactionDTO;
use Devolon\Payment\DTOs\TransactionResultDTO;
use Devolon\Payment\Resources\TransactionResultResource;
use Devolon\Payment\Tests\Stubs\OnlyDummyGatewayProductType;
use Devolon\Payment\Tests\Stubs\SampleProductType;
use Hamcrest\Core\IsEqual;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Mockery\MockInterface;
use Devolon\Payment\Actions\CreateTransactionAction;

class CreateTransactionControllerTest extends PaymentTestCase
{
    use WithFaker;

    private const ROUTE_NAME = 'app.payment.transaction.create';

    protected function tearDown(): void
    {
        Payment::clearProductTypes();
        parent::tearDown();
    }

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
        $this->assertEquals('/payment/transaction', $result);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function test422WithInvalidData($invalidFieldName, $invalidValue, $expectedValidationErrorKey)
    {
        // Arrange
        $this->tagDummyGateway();
        $this->tagSampleProductType();
        $createTransactionAction = $this->mockCreateTransactionAction();
        $transactionData = $this->transactionData();
        Arr::set($transactionData, $invalidFieldName, $invalidValue);
        $user = $this->getUserClass()::factory()->create();

        // Expect
        $createTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this->actingAs($user)->postJson(route(self::ROUTE_NAME), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([$expectedValidationErrorKey])
            ->assertJsonMissingValidationErrors(
                array_diff(array_keys($transactionData), [$expectedValidationErrorKey])
            );
    }

    /**
     * @dataProvider missingDataProvider
     */
    public function test422WithMissingRequiredField($missingFieldName)
    {
        // Arrange
        $this->tagDummyGateway();
        $this->tagSampleProductType();
        $createTransactionAction = $this->mockCreateTransactionAction();
        $transactionData = Arr::except($this->transactionData(), $missingFieldName);

        $user = $this->getUserClass()::factory()->create();

        // Expect
        $createTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this->actingAs($user)->postJson(route(self::ROUTE_NAME), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([$missingFieldName])
            ->assertJsonMissingValidationErrors(array_diff(array_keys($transactionData), [$missingFieldName]));
    }

    public function test422ForOnlyDummyGatewayProductType()
    {
        // Arrange
        $createTransactionAction = $this->mockCreateTransactionAction();
        $this->tagDummyTwoGateway();
        $productType = $this->tagOnlyDummyGatewayProductType();
        $transactionData = $this->transactionData();
        Arr::set($transactionData, 'product_type', $productType->getName());
        Arr::set($transactionData, 'payment_method', 'dummy_two');

        $user = $this->getUserClass()::factory()->create();

        // Expect
        $createTransactionAction->shouldNotReceive('__invoke');

        // Act
        $response = $this->actingAs($user)->postJson(route(self::ROUTE_NAME), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['product_type'])
            ->assertJsonMissingValidationErrors(array_diff(array_keys($transactionData), ['product_type']));
    }

    public function testSuccessWithProductClass()
    {
        // Arrange
        $this->tagDummyGateway();
        Payment::addProductType('dummy');
        $user = $this->getUserClass()::factory()->create();
        $createTransactionAction = $this->mockCreateTransactionAction();
        $transactionData = array_merge($this->transactionData(), [
            'user_id' => $user->id,
            'product_type' => 'dummy',
        ]);
        $transaction = Transaction::factory()->create($transactionData);
        $createTransactionDTO = CreateTransactionDTO::fromArray($transactionData);
        $transactionResultDTO = TransactionResultDTO::fromArray([
            'transaction' => $transaction,
            'should_redirect' => true,
            'redirect_to' => RedirectDTO::fromArray([
                'redirect_url' => $this->faker->url,
                'redirect_method' => $this->faker->word,
                'redirect_data' => [
                    $this->faker->word => $this->faker->word,
                ]
            ]),
        ]);


        // Expect
        $createTransactionAction
            ->shouldReceive('__invoke')
            ->with(IsEqual::equalTo($createTransactionDTO))
            ->once()
            ->andReturn($transactionResultDTO)
        ;

        // Act
        $response = $this->actingAs($user)->postJson(route(self::ROUTE_NAME), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(TransactionResultResource::make($transactionResultDTO)->response()->getData(true));
    }

    public function testSuccess()
    {
        // Arrange
        $user = $this->getUserClass()::factory()->create();
        $this->tagSampleProductType();
        $this->tagDummyGateway();
        $createTransactionAction = $this->mockCreateTransactionAction();
        $transactionData = array_merge($this->transactionData(), [
            'user_id' => $user->id,
        ]);
        $transaction = Transaction::factory()->create($transactionData);
        $createTransactionDTO = CreateTransactionDTO::fromArray($transactionData);
        $transactionResultDTO = TransactionResultDTO::fromArray([
            'transaction' => $transaction,
            'should_redirect' => true,
            'redirect_to' => RedirectDTO::fromArray([
                'redirect_url' => $this->faker->url,
                'redirect_method' => $this->faker->word,
                'redirect_data' => [
                    $this->faker->word => $this->faker->word,
                ]
            ]),
        ]);


        // Expect
        $createTransactionAction
            ->shouldReceive('__invoke')
            ->with(IsEqual::equalTo($createTransactionDTO))
            ->once()
            ->andReturn($transactionResultDTO)
        ;

        // Act
        $response = $this->actingAs($user)->postJson(route(self::ROUTE_NAME), $transactionData);

        // Assert
        $response
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson(TransactionResultResource::make($transactionResultDTO)->response()->getData(true));
    }

    public function invalidDataProvider(): array
    {
        $faker = $this->makeFaker('en_US');

        return [
            'product_type is wrong' => ['product_type', $faker->word, 'product_type'],
            'payment_method is wrong' => ['payment_method', $faker->word, 'payment_method'],
            'payment_method_data is not array' => ['payment_method_data', $faker->word, 'payment_method_data'],
            'payment_method_data has wrong key' => ['payment_method_data', [
                $faker->word => 'value',
            ], 'payment_method_data.key'],
            'payment_method_data has wrong value' => ['payment_method_data', [
                'key' => $faker->word,
            ], 'payment_method_data.key'],
            'product_data is not array' => ['product_data', $faker->word, 'product_data'],
            'product_data has wrong key' => ['product_data', [
                $faker->word => 'value',
            ], 'product_data.key'],
            'product_data has wrong value' => ['product_data', [
                'key' => $faker->word,
            ], 'product_data.key'],
        ];
    }

    public function missingDataProvider(): array
    {
        return [
            'payment_method is missing' => ['payment_method'],
            'product_type is missing' => ['product_type'],
        ];
    }

    private function transactionData(): array
    {
        return [
            'product_type' => 'sample_product',
            'payment_method' => 'dummy',
            'payment_method_data' => [
                'key' => 'value',
            ],
            'product_data' => [
                'key' => 'value',
            ],
        ];
    }

    private function mockCreateTransactionAction(): MockInterface
    {
        return $this->mock(CreateTransactionAction::class);
    }

    private function tagOnlyDummyGatewayProductType(): ProductTypeInterface
    {
        $this->app->tag(OnlyDummyGatewayProductType::class, ProductTypeInterface::class);

        return resolve(OnlyDummyGatewayProductType::class);
    }

    public function tagSampleProductType()
    {
        $this->app->tag(SampleProductType::class, ProductTypeInterface::class);
    }

    public function tagDummyGateway()
    {
        $this->app->tag(DummyGateway::class, PaymentGatewayInterface::class);
    }

    public function tagDummyTwoGateway()
    {
        $this->app->tag(DummyTwoGateway::class, PaymentGatewayInterface::class);
    }
}
