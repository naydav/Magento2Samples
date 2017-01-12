<?php
namespace Engine\Category\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * @param int $categoryId
     * @return \Engine\Category\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($categoryId);

    /**
     * Throw CouldNotDeleteException If try to delete root category
     *
     * @param int $categoryId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($categoryId);

    /**
     * @param \Engine\Category\Api\Data\CategoryInterface $category
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Engine\Framework\Exception\ValidatorException
     */
    public function save(\Engine\Category\Api\Data\CategoryInterface $category);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Category\Api\Data\CategorySearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
