<?php
namespace Engine\CharacteristicGroup\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CharacteristicGroupRepositoryInterface
{
    /**
     * @param int $characteristicGroupId
     * @return \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($characteristicGroupId);

    /**
     * @param int $characteristicGroupId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($characteristicGroupId);

    /**
     * @param \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface $characteristicGroup
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Engine\MagentoFix\Exception\ValidatorException
     */
    public function save(\Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface $characteristicGroup);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
