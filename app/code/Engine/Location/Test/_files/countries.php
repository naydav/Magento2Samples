<?php
declare(strict_types=1);

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountryInterfaceFactory;
use Engine\Location\Api\CountryRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CountryInterfaceFactory $countryFactory */
$countryFactory = Bootstrap::getObjectManager()->get(CountryInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var CountryRepositoryInterface $countryRepository */
$countryRepository = Bootstrap::getObjectManager()->get(CountryRepositoryInterface::class);

$countriesData = [
    [
        CountryInterface::COUNTRY_ID => 100,
        CountryInterface::ENABLED => true,
        CountryInterface::POSITION => 300,
        CountryInterface::NAME => 'Country-name-1',
    ],
    [
        CountryInterface::COUNTRY_ID => 200,
        CountryInterface::ENABLED => true,
        CountryInterface::POSITION => 200,
        CountryInterface::NAME => 'Country-name-2',
    ],
    [
        CountryInterface::COUNTRY_ID => 300,
        CountryInterface::ENABLED => false,
        CountryInterface::POSITION => 200,
        CountryInterface::NAME => 'Country-name-2',
    ],
    [
        CountryInterface::COUNTRY_ID => 400,
        CountryInterface::ENABLED => false,
        CountryInterface::POSITION => 100,
        CountryInterface::NAME => 'Country-name-3',
    ],
];
foreach ($countriesData as $countryData) {
    /** @var CountryInterface $country */
    $country = $countryFactory->create();
    $dataObjectHelper->populateWithArray($country, $countryData, CountryInterface::class);
    $countryRepository->save($country);
}
