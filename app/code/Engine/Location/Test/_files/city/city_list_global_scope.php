<?php
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityInterfaceFactory $cityFactory */
$cityFactory = Bootstrap::getObjectManager()->get(CityInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);

require '../../../app/code/Engine/Location/Test/_files/region/region_id_100.php';

$citiesData = [
    [
        CityInterface::CITY_ID => 100,
        CityInterface::REGION_ID => 100,
        CityInterface::IS_ENABLED => true,
        CityInterface::POSITION => 300,
        CityInterface::TITLE => 'City-title-3',
    ],
    [
        CityInterface::CITY_ID => 200,
        CityInterface::REGION_ID => 100,
        CityInterface::IS_ENABLED => true,
        CityInterface::POSITION => 200,
        CityInterface::TITLE => 'City-title-2',
    ],
    [
        CityInterface::CITY_ID => 300,
        CityInterface::IS_ENABLED => false,
        CityInterface::POSITION => 200,
        CityInterface::TITLE => 'City-title-2',
    ],
    [
        CityInterface::CITY_ID => 400,
        CityInterface::IS_ENABLED => false,
        CityInterface::POSITION => 100,
        CityInterface::TITLE => 'City-title-1',
    ],
];
foreach ($citiesData as $cityData) {
    /** @var CityInterface $city */
    $city = $cityFactory->create();
    $city = $hydrator->hydrate($city, $cityData);
    $cityRepository->save($city);
}
