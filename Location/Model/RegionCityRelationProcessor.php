<?php
namespace Engine\Location\Model;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\City\CitiesByRegionList;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionCityRelationProcessor
{
    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var CitiesByRegionList
     */
    private $citiesByRegionList;

    /**
     * @var CityInterfaceFactory
     */
    private $cityFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param RegionRepositoryInterface $regionRepository
     * @param CityRepositoryInterface $cityRepository
     * @param CitiesByRegionList $citiesByRegionList
     * @param CityInterfaceFactory $cityFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        RegionRepositoryInterface $regionRepository,
        CityRepositoryInterface $cityRepository,
        CitiesByRegionList $citiesByRegionList,
        CityInterfaceFactory $cityFactory,
        HydratorInterface $hydrator
    ) {
        $this->regionRepository = $regionRepository;
        $this->cityRepository = $cityRepository;
        $this->citiesByRegionList = $citiesByRegionList;
        $this->cityFactory = $cityFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function process($regionId, array $citiesData)
    {
        try {
            $this->doProcess($regionId, $citiesData);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Cannot assign cities to region'), $e);
        }
        return true;
    }

    /**
     * @param int $regionId
     * @param array $citiesData
     */
    private function doProcess($regionId, array $citiesData)
    {
        $region = $this->regionRepository->get($regionId);
        $result = $this->citiesByRegionList->getList($region->getRegionId());
        $currentAssignedCities = $result->getItems();

        $currentAssignedCitiesMap = [];
        foreach ($currentAssignedCities as $currentAssignedCity) {
            $currentAssignedCitiesMap[$currentAssignedCity->getCityId()] = $currentAssignedCity;
        }

        foreach ($citiesData as $key => $cityData) {
            if (empty($cityData['id'])) {
                /** @var CityInterface $city */
                $city = $this->cityFactory->create();
                $this->hydrator->hydrate($city, $cityData);
            } else {
                $cityId = $cityData['id'];
                $city = $this->cityRepository->get($cityId);
                unset($currentAssignedCitiesMap[$cityId]);
            }
            $position = ($key + 1) * 10;
            $city->setPosition($position);
            $city->setRegionId($region->getRegionId());
            $this->cityRepository->save($city);
        }

        foreach ($currentAssignedCitiesMap as $currentAssignedCity) {
            $currentAssignedCity->setRegionId(null);
            $this->cityRepository->save($currentAssignedCity);
        }
    }
}
