<?php

namespace Jeffersongoncalves\Webflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array listSites()
 * @method static array getSite(string $siteId)
 * @method static array publishSite(string $siteId, array $customDomains = [], bool $publishToWebflowSubdomain = false)
 * @method static array listCollections(string $siteId)
 * @method static array listCollectionItems(string $collectionId, ?int $limit = null, ?int $offset = null)
 * @method static array getCollectionItem(string $collectionId, string $itemId)
 * @method static array createCollectionItem(string $collectionId, array $fieldData, bool $isDraft = false, bool $isArchived = false)
 * @method static array updateCollectionItem(string $collectionId, string $itemId, array $fieldData)
 * @method static array publishCollectionItems(string $collectionId, array $itemIds)
 * @method static array listForms(string $siteId)
 * @method static array listFormSubmissions(string $formId, ?int $limit = null, ?int $offset = null)
 *
 * @see \Jeffersongoncalves\Webflow\Webflow
 */
class Webflow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Jeffersongoncalves\Webflow\Webflow::class;
    }
}
