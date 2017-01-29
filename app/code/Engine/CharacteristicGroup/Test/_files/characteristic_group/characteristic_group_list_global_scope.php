<?php
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterfaceFactory;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CharacteristicGroupInterfaceFactory $characteristicGroupFactory */
$characteristicGroupFactory = Bootstrap::getObjectManager()->get(CharacteristicGroupInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CharacteristicGroupRepositoryInterface $characteristicGroupRepository */
$characteristicGroupRepository = Bootstrap::getObjectManager()->get(CharacteristicGroupRepositoryInterface::class);

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
foreach ($characteristicGroupsData as $characteristicGroupData) {
    /** @var CharacteristicGroupInterface $characteristicGroup */
    $characteristicGroup = $characteristicGroupFactory->create();
    $characteristicGroup = $hydrator->hydrate($characteristicGroup, $characteristicGroupData);
    $characteristicGroupRepository->save($characteristicGroup);
}
