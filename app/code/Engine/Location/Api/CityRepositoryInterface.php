<?php
namespace Engine\Location\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CityRepositoryInterface
{
    /**
     * @param int $cityId
     * @return \Engine\Location\Api\Data\CityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($cityId);

    /**
     * @param int $cityId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($cityId);

    /**
     * @param \Engine\Location\Api\Data\CityInterface $city
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Engine\MagentoFix\Exception\ValidatorException
     */
    public function save(\Engine\Location\Api\Data\CityInterface $city);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Engine\Location\Api\Data\CitySearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
