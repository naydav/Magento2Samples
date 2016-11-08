<?php
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);

/** @var RegionInterface $region */
$region = $regionFactory->create();
$region = $hydrator->hydrate($region, [
    RegionInterface::REGION_ID => 100,
    RegionInterface::TITLE => 'title-0',
    RegionInterface::IS_ENABLED => true,
    RegionInterface::POSITION => 1000,
]);
$regionId = $regionRepository->save($region);

// save per store data
require 'store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

$region = $regionRepository->get($regionId);
$region->setTitle('per-store-' . $region->getTitle());
$regionRepository->save($region);

$storeManager->setCurrentStore($currentStore);
