<?php
namespace Engine\CategoryTree\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategoryTreeLoaderInterface
{
    /**
     * @param int|null $categoryId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\CategoryTree\Api\Data\CategoryTreeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTree($categoryId = null, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);
}
