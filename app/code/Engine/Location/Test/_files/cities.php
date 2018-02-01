<?php
declare(strict_types=1);

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityInterfaceFactory $cityFactory */
$cityFactory = Bootstrap::getObjectManager()->get(CityInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);

$citiesData = [
    [
        CityInterface::CITY_ID => 100,
        CityInterface::REGION_ID => 300,
        CityInterface::ENABLED => true,
        CityInterface::POSITION => 300,
        CityInterface::NAME => 'City-name-1',
    ],
    [
        CityInterface::CITY_ID => 200,
        CityInterface::REGION_ID => 200,
        CityInterface::ENABLED => true,
        CityInterface::POSITION => 200,
        CityInterface::NAME => 'City-name-2',
    ],
    [
        CityInterface::CITY_ID => 300,
        CityInterface::REGION_ID => 200,
        CityInterface::ENABLED => false,
        CityInterface::POSITION => 200,
        CityInterface::NAME => 'City-name-2',
    ],
    [
        CityInterface::CITY_ID => 400,
        CityInterface::REGION_ID => 100,
        CityInterface::ENABLED => false,
        CityInterface::POSITION => 100,
        CityInterface::NAME => 'City-name-3',
    ],
];
foreach ($citiesData as $cityData) {
    /** @var CityInterface $city */
    $city = $cityFactory->create();
    $dataObjectHelper->populateWithArray($city, $cityData, CityInterface::class);
    $cityRepository->save($city);
}
