<?php

namespace Devolon\Payment\Tests\Unit\Resources;

use Devolon\Payment\DTOs\RedirectDTO;
use Devolon\Payment\Tests\PaymentTestCase;
use Devolon\Payment\Resources\RedirectDTOResource;
use Illuminate\Foundation\Testing\WithFaker;

class RedirectDTOResourceTest extends PaymentTestCase
{
    use WithFaker;

    public function testResponse()
    {
        // Arrange
        $redirectData = [
            'redirect_url' => $this->faker->url,
            'redirect_method' => $this->faker->word,
            'redirect_data' => [
                $this->faker->word => $this->faker->word,
            ]
        ];
        $redirectDTO = RedirectDTO::fromArray($redirectData);

        // Act
        $result = RedirectDTOResource::make($redirectDTO)->response()->getData(true);

        // Assert
        $this->assertEquals($redirectData, $result);
    }
}
