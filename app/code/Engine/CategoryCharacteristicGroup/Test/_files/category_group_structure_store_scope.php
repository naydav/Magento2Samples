<?php
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterfaceFactory;
use Engine\CategoryCharacteristicGroup\Api\RelationRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Fixture Structure:
 *
 * Categories:
 * Category-1 (id:100)
 * Category-2 (id:200)
 * Category-3 (id:300)
 *
 * Characteristic Groups:
 * Characteristic-Group-1 (id:100)
 * Characteristic-Group-2 (id:200)
 * Characteristic-Group-3 (id:300)
 *
 * Relations:
 * Category-2 (id:200)
 *   Characteristic-Group-2 (id:200, position:1)
 *   Characteristic-Group-1 (id:100, position:2)
 * Category-3 (id:300)
 *   Characteristic-Group-2 (id:200, position:1)
 */

// Create Categories
require '../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php';
require '../../../app/code/Engine/Category/Test/_files/category/category_id_200_store_scope.php';
require '../../../app/code/Engine/Category/Test/_files/category/category_id_300_store_scope.php';

// Create Characteristic Groups
require '../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100_store_scope.php';
require '../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_200_store_scope.php';
require '../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_300_store_scope.php';

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
        RelationInterface::CATEGORY_ID => 200,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
        RelationInterface::CHARACTERISTIC_GROUP_POSITION => 2,
    ],
    [
        RelationInterface::RELATION_ID => 200,
        RelationInterface::CATEGORY_ID => 200,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
        RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
    ],
    [
        RelationInterface::RELATION_ID => 300,
        RelationInterface::CATEGORY_ID => 300,
        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
        RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
    ],
];
foreach ($relationsData as $relationData) {
    /** @var RelationInterface $relation */
    $relation = $relationFactory->create();
    $relation = $hydrator->hydrate($relation, $relationData);
    $relationRepository->save($relation);
}
