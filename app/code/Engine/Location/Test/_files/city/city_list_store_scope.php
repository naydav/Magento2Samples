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

require '../../../app/code/Engine/Location/Test/_files/region/region.php';

$citiesData = [
    [
        CityInterface::CITY_ID => 100,
        CityInterface::REGION_ID => 100,
        CityInterface::TITLE => 'city-3',
        CityInterface::IS_ENABLED => true,
        CityInterface::POSITION => 300,
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
        CityInterface::POSITION => 100,
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
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

foreach ($cityIds as $key => $cityId) {
    if ($key === 1) {
        // skip creating per store value for second entity
        continue;
    }
    $city = $cityRepository->get($cityId);
    $city->setTitle('z-per-store-' . $city->getTitle());
    $cityRepository->save($city);
}
$storeManager->setCurrentStore($currentStore);
