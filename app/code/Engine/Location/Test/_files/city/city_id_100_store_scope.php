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

/** @var CityInterface $city */
$city = $cityFactory->create();
$city = $hydrator->hydrate($city, [
    CityInterface::CITY_ID => 100,
    CityInterface::REGION_ID => 100,
    CityInterface::IS_ENABLED => true,
    CityInterface::POSITION => 100,
    CityInterface::TITLE => 'City-title-100',
]);
$cityId = $cityRepository->save($city);

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

$city = $cityRepository->get($cityId);
$city->setTitle($city->getTitle() . '-per-store');
$cityRepository->save($city);
