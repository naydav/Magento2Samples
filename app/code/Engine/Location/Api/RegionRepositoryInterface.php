<?php
namespace Engine\Location\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RegionRepositoryInterface
{
    /**
     * @param int $regionId
     * @return \Engine\Location\Api\Data\RegionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($regionId);

    /**
     * @param int $regionId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($regionId);

    /**
     * @param \Engine\Location\Api\Data\RegionInterface $region
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Engine\MagentoFix\Exception\ValidatorException
     */
    public function save(\Engine\Location\Api\Data\RegionInterface $region);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Location\Api\Data\RegionSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
