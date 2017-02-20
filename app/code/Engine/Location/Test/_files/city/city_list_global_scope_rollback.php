<?php
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
/** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
$searchCriteriaBuilderFactory = Bootstrap::getObjectManager()->get(SearchCriteriaBuilderFactory::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();

$searchCriteria = $searchCriteriaBuilder
    ->addFilter(CityInterface::CITY_ID, [100, 200, 300, 400], 'in')
    ->create();

$cities = $cityRepository->getList($searchCriteria)->getItems();
foreach ($cities as $city) {
    $cityRepository->deleteById($city->getCityId());
}

require_once '../../../app/code/Engine/Location/Test/_files/region/region_id_100_rollback.php';
