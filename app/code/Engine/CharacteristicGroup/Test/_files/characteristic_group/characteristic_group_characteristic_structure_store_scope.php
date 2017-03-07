<?php
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterfaceFactory;
use Engine\CharacteristicGroup\Api\RelationRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Fixture Structure:
 *
 * Characteristic Groups:
 * Characteristic-Group-1 (id:100)
 * Characteristic-Group-2 (id:200)
 * Characteristic-Group-3 (id:300)
 *
 * Characteristic:
 * Characteristic-1 (id:100)
 * Characteristic-2 (id:200)
 * Characteristic-3 (id:300)
 *
 * Relations:
 * Characteristic-Group-2 (id:200)
 *   Characteristic-2 (id:200, position:1)
 *   Characteristic-1 (id:100, position:2)
 * Characteristic-Group-3 (id:300)
 *   Characteristic-2 (id:200, position:1)
 */

// Create Characteristic Groups
require 'characteristic_group_id_100_store_scope.php';
require 'characteristic_group_id_200_store_scope.php';
require 'characteristic_group_id_300_store_scope.php';

// Create Characteristic
require '../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_id_100_store_scope.php';
require '../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_id_200_store_scope.php';
require '../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_id_300_store_scope.php';

// Create Relations
/** @var RelationInterfaceFactory $relationFactory */
$relationFactory = Bootstrap::getObjectManager()->get(RelationInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var RelationRepositoryInterface $relationRepository */
$relationRepository = Bootstrap::getObjectManager()->get(RelationRepositoryInterface::class);

$relationsData = [
    [
        RelationInterface::RELATION_ID => 100,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
        RelationInterface::CHARACTERISTIC_ID => 100,
        RelationInterface::CHARACTERISTIC_POSITION => 2,
    ],
    [
        RelationInterface::RELATION_ID => 200,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
        RelationInterface::CHARACTERISTIC_ID => 200,
        RelationInterface::CHARACTERISTIC_POSITION => 1,
    ],
    [
        RelationInterface::RELATION_ID => 300,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 300,
        RelationInterface::CHARACTERISTIC_ID => 200,
        RelationInterface::CHARACTERISTIC_POSITION => 1,
    ],
];
foreach ($relationsData as $relationData) {
    /** @var RelationInterface $relation */
    $relation = $relationFactory->create();
    $relation = $hydrator->hydrate($relation, $relationData);
    $relationRepository->save($relation);
}
