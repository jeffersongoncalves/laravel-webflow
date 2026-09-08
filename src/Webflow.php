<?php

namespace Jeffersongoncalves\Webflow;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Jeffersongoncalves\Webflow\Exceptions\WebflowException;

/**
 * Thin wrapper around Laravel's Http client for the Webflow REST API v2
 * (https://developers.webflow.com/data/reference/rest-introduction),
 * authenticated with a Bearer token.
 *
 * Webflow enforces 60 requests/min per site for general endpoints and
 * 10 requests/min for publishing endpoints — this client does not throttle
 * client-side, so callers should handle 429 responses (surfaced as a
 * WebflowException) if they publish in bulk.
 */
class Webflow
{
    public function __construct(
        protected string $token,
        protected string $baseUrl = 'https://api.webflow.com/v2',
    ) {}

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withToken($this->token)
            ->acceptJson();
    }

    /** @param array<string, mixed> $query */
    protected function request(string $method, string $path, array $query = [], ?array $body = null): array
    {
        $response = $this->client()->{$method}($path, $body ?? $query);

        if ($response->failed()) {
            throw WebflowException::fromResponse($response);
        }

        return (array) ($response->json() ?? []);
    }

    protected function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, array_filter($query, fn ($value) => $value !== null));
    }

    // --- Sites ---

    /** @return array<string, mixed> */
    public function listSites(): array
    {
        return $this->get('/sites');
    }

    /** @return array<string, mixed> */
    public function getSite(string $siteId): array
    {
        return $this->get("/sites/{$siteId}");
    }

    /** @return array<string, mixed> */
    public function publishSite(string $siteId, array $customDomains = [], bool $publishToWebflowSubdomain = false): array
    {
        return $this->request('post', "/sites/{$siteId}/publish", body: [
            'customDomains' => $customDomains,
            'publishToWebflowSubdomain' => $publishToWebflowSubdomain,
        ]);
    }

    // --- Collections ---

    /** @return array<string, mixed> */
    public function listCollections(string $siteId): array
    {
        return $this->get("/sites/{$siteId}/collections");
    }

    // --- Collection Items ---

    /** @return array<string, mixed> */
    public function listCollectionItems(string $collectionId, ?int $limit = null, ?int $offset = null): array
    {
        return $this->get("/collections/{$collectionId}/items", [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /** @return array<string, mixed> */
    public function getCollectionItem(string $collectionId, string $itemId): array
    {
        return $this->get("/collections/{$collectionId}/items/{$itemId}");
    }

    /**
     * @param  array<string, mixed>  $fieldData
     * @return array<string, mixed>
     */
    public function createCollectionItem(string $collectionId, array $fieldData, bool $isDraft = false, bool $isArchived = false): array
    {
        return $this->request('post', "/collections/{$collectionId}/items", body: [
            'fieldData' => $fieldData,
            'isDraft' => $isDraft,
            'isArchived' => $isArchived,
        ]);
    }

    /**
     * @param  array<string, mixed>  $fieldData
     * @return array<string, mixed>
     */
    public function updateCollectionItem(string $collectionId, string $itemId, array $fieldData): array
    {
        return $this->request('patch', "/collections/{$collectionId}/items/{$itemId}", body: [
            'fieldData' => $fieldData,
        ]);
    }

    /**
     * @param  string[]  $itemIds
     * @return array<string, mixed>
     */
    public function publishCollectionItems(string $collectionId, array $itemIds): array
    {
        return $this->request('post', "/collections/{$collectionId}/items/publish", body: [
            'itemIds' => $itemIds,
        ]);
    }

    // --- Forms ---

    /** @return array<string, mixed> */
    public function listForms(string $siteId): array
    {
        return $this->get("/sites/{$siteId}/forms");
    }

    /** @return array<string, mixed> */
    public function listFormSubmissions(string $formId, ?int $limit = null, ?int $offset = null): array
    {
        return $this->get("/forms/{$formId}/submissions", [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
