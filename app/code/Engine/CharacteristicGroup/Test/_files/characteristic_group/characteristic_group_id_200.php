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

/** @var CharacteristicGroupInterface $characteristicGroup */
$characteristicGroup = $characteristicGroupFactory->create();
$characteristicGroup = $hydrator->hydrate($characteristicGroup, [
    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
    CharacteristicGroupInterface::IS_ENABLED => true,
    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-200',
    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-200',
    CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-200',
]);
$characteristicGroupRepository->save($characteristicGroup);
