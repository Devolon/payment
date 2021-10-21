<?php

namespace Devolon\Payment\DTOs;

use Devolon\Common\Bases\DTO;

class PurchaseResultDTO extends DTO
{
    public function __construct(
        public bool $should_redirect,
        public ?RedirectDTO $redirect_to
    ) {
    }
}
