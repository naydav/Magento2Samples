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

/** @var CityInterface $city */
$city = $cityFactory->create();
$city = $hydrator->hydrate($city, [
    CityInterface::CITY_ID => 100,
    CityInterface::TITLE => 'title-0',
    CityInterface::IS_ENABLED => true,
    CityInterface::POSITION => 1000,
]);
$cityId = $cityRepository->save($city);

// save per store data
require 'store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

$city = $cityRepository->get($cityId);
$city->setTitle('per-store-' . $city->getTitle());
$cityRepository->save($city);

$storeManager->setCurrentStore($currentStore);
