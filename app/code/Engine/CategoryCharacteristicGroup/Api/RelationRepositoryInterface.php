<?php
namespace Engine\CategoryCharacteristicGroup\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RelationRepositoryInterface
{
    /**
     * @param int $relationId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($relationId);

    /**
     * @param \Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface $relation
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface $relation);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\CategoryCharacteristicGroup\Api\Data\RelationSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
