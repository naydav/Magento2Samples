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

require '../../../app/code/Engine/Location/Test/_files/region/region.php';

$citiesData = [
    [
        CityInterface::CITY_ID => 100,
        CityInterface::REGION_ID => 100,
        CityInterface::TITLE => 'city-3',
        CityInterface::IS_ENABLED => true,
        CityInterface::POSITION => 100,
    ],
    [
        CityInterface::CITY_ID => 200,
        CityInterface::REGION_ID => 100,
        CityInterface::TITLE => 'city-2',
        CityInterface::IS_ENABLED => true,
        CityInterface::POSITION => 200,
    ],
    [
        CityInterface::CITY_ID => 300,
        CityInterface::TITLE => 'city-2',
        CityInterface::IS_ENABLED => false,
        CityInterface::POSITION => 200,
    ],
    [
        CityInterface::CITY_ID => 400,
        CityInterface::TITLE => 'city-1',
        CityInterface::IS_ENABLED => false,
        CityInterface::POSITION => 300,
    ],
];
foreach ($citiesData as $cityData) {
    /** @var CityInterface $city */
    $city = $cityFactory->create();
    $city = $hydrator->hydrate($city, $cityData);
    $cityRepository->save($city);
}
