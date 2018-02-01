<?php
declare(strict_types=1);

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\CountryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CountryRepositoryInterface $countryRepository */
$countryRepository = Bootstrap::getObjectManager()->get(CountryRepositoryInterface::class);
/** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
$searchCriteriaBuilderFactory = Bootstrap::getObjectManager()->get(SearchCriteriaBuilderFactory::class);
/** @var SearchCriteriaBuilder $searchCriteriaBuilder */
$searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();

$searchCriteria = $searchCriteriaBuilder
    ->addFilter(CountryInterface::COUNTRY_ID, [100, 200, 300, 400], 'in')
    ->create();

$countries = $countryRepository->getList($searchCriteria)->getItems();
foreach ($countries as $country) {
    $countryRepository->deleteById((int)$country->getCountryId());
}
