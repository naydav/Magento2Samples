<?php
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CharacteristicGroupRepositoryInterface $characteristicGroupRepository */
$characteristicGroupRepository = Bootstrap::getObjectManager()->get(CharacteristicGroupRepositoryInterface::class);
/** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
$searchCriteriaBuilderFactory = Bootstrap::getObjectManager()->get(SearchCriteriaBuilderFactory::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();

$searchCriteria = $searchCriteriaBuilder
    ->addFilter(CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID, [100, 200, 300, 400], 'in')
    ->create();

$characteristicGroups = $characteristicGroupRepository->getList($searchCriteria)->getItems();
foreach ($characteristicGroups as $characteristicGroup) {
    $characteristicGroupRepository->deleteById($characteristicGroup->getCharacteristicGroupId());
}
