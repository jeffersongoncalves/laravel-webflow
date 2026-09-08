<?php

namespace Jeffersongoncalves\Webflow\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class WebflowException extends RuntimeException
{
    /** @var array<string, mixed> */
    protected array $errorBody = [];

    public static function fromResponse(Response $response): self
    {
        $body = (array) ($response->json() ?? []);

        $detail = $body['message'] ?? $body['error'] ?? $response->body();

        $exception = new self(
            "Webflow API error (HTTP {$response->status()}): {$detail}",
            $response->status(),
        );
        $exception->errorBody = $body;

        return $exception;
    }

    /** @return array<string, mixed> */
    public function errorBody(): array
    {
        return $this->errorBody;
    }
}
