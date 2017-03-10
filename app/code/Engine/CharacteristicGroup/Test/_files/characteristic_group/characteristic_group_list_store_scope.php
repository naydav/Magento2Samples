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

$characteristicGroupsData = [
    [
        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
        CharacteristicGroupInterface::IS_ENABLED => true,
        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-3',
        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-3',
        CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-3',
    ],
    [
        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
        CharacteristicGroupInterface::IS_ENABLED => true,
        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-2',
        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
        CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-2',
    ],
    [
        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
        CharacteristicGroupInterface::IS_ENABLED => false,
        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-2',
        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
        CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-2',
    ],
    [
        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 400,
        CharacteristicGroupInterface::IS_ENABLED => false,
        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-1',
        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-1',
        CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-1',
    ],
];
$characteristicGroupIds = [];
foreach ($characteristicGroupsData as $characteristicGroupData) {
    /** @var CharacteristicGroupInterface $characteristicGroup */
    $characteristicGroup = $characteristicGroupFactory->create();
    $characteristicGroup = $hydrator->hydrate($characteristicGroup, $characteristicGroupData);
    $characteristicGroupIds[] = $characteristicGroupRepository->save($characteristicGroup);
}

// save per store data
require '../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php';
$customStoreCode = 'test_store';
$storeManager->setCurrentStore($customStoreCode);

foreach ($characteristicGroupIds as $key => $characteristicGroupId) {
    if ($key === 1) {
        // skip creating per store value for second entity
        continue;
    }
    $characteristicGroup = $characteristicGroupRepository->get($characteristicGroupId);
    // 'z-sort' prefix is need for sorting change in store scope
    $characteristicGroup->setTitle('z-sort-' . $characteristicGroup->getTitle() . '-per-store');
    $characteristicGroup->setDescription('z-sort-' . $characteristicGroup->getDescription() . '-per-store');
    $characteristicGroupRepository->save($characteristicGroup);
}
