<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CitySearchResultInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class CityRepository implements CityRepositoryInterface
{
    /**
     * @var SaveCityInterface
     */
    private $saveCity;

    /**
     * @var GetCityInterface
     */
    private $getCity;

    /**
     * @var DeleteCityByIdInterface
     */
    private $deleteCityById;

    /**
     * @var GetCityListInterface
     */
    private $getCityList;

    /**
     * @param SaveCityInterface $saveCity
     * @param GetCityInterface $getCity
     * @param DeleteCityByIdInterface $deleteCityById
     * @param GetCityListInterface $getCityList
     */
    public function __construct(
        SaveCityInterface $saveCity,
        GetCityInterface $getCity,
        DeleteCityByIdInterface $deleteCityById,
        GetCityListInterface $getCityList
    ) {
        $this->saveCity = $saveCity;
        $this->getCity = $getCity;
        $this->deleteCityById = $deleteCityById;
        $this->getCityList = $getCityList;
    }

    /**
     * @inheritdoc
     */
    public function save(CityInterface $city): int
    {
        return $this->saveCity->execute($city);
    }

    /**
     * @inheritdoc
     */
    public function get(int $cityId): CityInterface
    {
        return $this->getCity->execute($cityId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $cityId)
    {
        $this->deleteCityById->execute($cityId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): CitySearchResultInterface
    {
        return $this->getCityList->execute($searchCriteria);
    }
}
