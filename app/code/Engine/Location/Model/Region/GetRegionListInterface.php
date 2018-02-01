<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionSearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Find Regions by SearchCriteria command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial GetList call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\RegionRepositoryInterface
 * @api
 */
interface GetRegionListInterface
{
    /**
     * Find Regions by given SearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return RegionSearchResultInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): RegionSearchResultInterface;
}
