<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionSearchResultInterface;
use Magento\Framework\Api\SearchResults;

/**
 * @inheritdoc
 */
class RegionSearchResult extends SearchResults implements RegionSearchResultInterface
{
}
