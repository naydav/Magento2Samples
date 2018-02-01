<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountrySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Find Countries by SearchCriteria command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial GetList call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CountryRepositoryInterface
 * @api
 */
interface GetCountryListInterface
{
    /**
     * Find Countries by given SearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CountrySearchResultInterface
     */
    public function execute(SearchCriteriaInterface $searchCriteria): CountrySearchResultInterface;
}
