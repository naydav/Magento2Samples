<?php
namespace Engine\CharacteristicGroup\Api;

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
     * @param \Engine\CharacteristicGroup\Api\Data\RelationInterface $relation
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Engine\CharacteristicGroup\Api\Data\RelationInterface $relation);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\CharacteristicGroup\Api\Data\RelationSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
