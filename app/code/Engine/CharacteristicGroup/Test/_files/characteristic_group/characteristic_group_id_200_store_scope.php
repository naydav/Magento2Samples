<?php
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterfaceFactory;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CharacteristicGroupInterfaceFactory $characteristicGroupFactory */
$characteristicGroupFactory = Bootstrap::getObjectManager()->get(CharacteristicGroupInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CharacteristicGroupRepositoryInterface $characteristicGroupRepository */
$characteristicGroupRepository = Bootstrap::getObjectManager()->get(CharacteristicGroupRepositoryInterface::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);

/** @var CharacteristicGroupInterface $characteristicGroup */
$characteristicGroup = $characteristicGroupFactory->create();
$characteristicGroup = $hydrator->hydrate($characteristicGroup, [
    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
    CharacteristicGroupInterface::IS_ENABLED => true,
    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-200',
    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-200',
    CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-200',
]);
$characteristicGroupId = $characteristicGroupRepository->save($characteristicGroup);

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$currentStore = $storeManager->getStore()->getCode();
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

$characteristicGroup = $characteristicGroupRepository->get($characteristicGroupId);
$characteristicGroup->setTitle($characteristicGroup->getTitle() . '-per-store');
$characteristicGroup->setDescription($characteristicGroup->getDescription() . '-per-store');
$characteristicGroupRepository->save($characteristicGroup);

$storeManager->setCurrentStore($currentStore);
