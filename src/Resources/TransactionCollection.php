<?php

namespace Devolon\Payment\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * swagger doc is in virtual directory (SwaggerResponses)
 */
class TransactionCollection extends ResourceCollection
{
    public $collects = TransactionResource::class;
}
