<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CitySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Find Cities by SearchCriteria command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial GetList call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CityRepositoryInterface
 * @api
 */
interface GetCityListInterface
{
    /**
     * Find Cities by given SearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CitySearchResultInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): CitySearchResultInterface;
}
