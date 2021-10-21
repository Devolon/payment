<?php

namespace Devolon\Payment\Resources;

use Devolon\Common\Bases\Resource;
use Devolon\Payment\Models\Transaction;
use Devolon\Payment\DTOs\RedirectDTO;
use Symfony\Component\HttpFoundation\Response;

/**
 * swagger doc is in virtual directory (SwaggerResponses)
 *
 * @property Transaction transaction
 * @property bool should_redirect
 * @property ?RedirectDTO redirect_to
 */
class TransactionResultResource extends Resource
{
    public function toArray($request): array
    {
        return [
            'transaction' => TransactionResource::make($this->transaction),
            'should_redirect' => $this->should_redirect,
            'redirect_to' => $this->should_redirect && $this->redirect_to ?
                RedirectDTOResource::make($this->redirect_to) : null,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(Response::HTTP_CREATED);

        parent::withResponse($request, $response);
    }
}
