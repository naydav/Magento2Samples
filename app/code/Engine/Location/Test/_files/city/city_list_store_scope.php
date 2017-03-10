<?php
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityInterfaceFactory $cityFactory */
$cityFactory = Bootstrap::getObjectManager()->get(CityInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);

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
$cityIds = [];
foreach ($citiesData as $cityData) {
    /** @var CityInterface $city */
    $city = $cityFactory->create();
    $city = $hydrator->hydrate($city, $cityData);
    $cityIds[] = $cityRepository->save($city);
}

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

foreach ($cityIds as $key => $cityId) {
    if ($key === 1) {
        // skip creating per store value for second entity
        continue;
    }
    $city = $cityRepository->get($cityId);
    // 'z-sort' prefix is need for sorting change in store scope
    $city->setTitle('z-sort-' . $city->getTitle() . '-per-store');
    $cityRepository->save($city);
}
