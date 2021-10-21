<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;

class RedirectDTO extends DTO
{
    public function __construct(
        public string $redirect_url,
        public string $redirect_method,
        public ?array $redirect_data,
    ) {
    }
}
