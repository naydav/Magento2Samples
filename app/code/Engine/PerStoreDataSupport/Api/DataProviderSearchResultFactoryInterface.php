<?php
namespace Engine\PerStoreDataSupport\Api;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface DataProviderSearchResultFactoryInterface
{
    /**
     * @param array $items
     * @param int $totalCount
     * @param SearchCriteriaInterface SearchCriteriaInterface $searchCriteria
     * @param string $idFieldName
     * @return SearchResultInterface
     */
    public function create(array $items, $totalCount, SearchCriteriaInterface $searchCriteria, $idFieldName);
}
