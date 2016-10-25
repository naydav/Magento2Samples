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
     */
    public function get($regionId);

    /**
     * @param int $regionId
     * @return void
     */
    public function deleteById($regionId);

    /**
     * @param \Engine\Location\Api\Data\RegionInterface $region
     * @return \Engine\Location\Api\Data\RegionInterface
     */
    public function save(\Engine\Location\Api\Data\RegionInterface $region);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Location\Api\Data\RegionSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
