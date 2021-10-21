<?php

namespace Devolon\Payment\Resources;

use Devolon\Common\Bases\Resource;

/**
 * swagger doc is in virtual directory (SwaggerResponses)
 *
 * @property string redirect_url
 * @property string redirect_method
 * @property array redirect_data
 */
class RedirectDTOResource extends Resource
{
    public function toArray($request)
    {
        return [
            'redirect_url' => $this->redirect_url,
            'redirect_method' => $this->redirect_method,
            'redirect_data' => $this->redirect_data,
        ];
    }
}
