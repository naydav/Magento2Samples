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

/** @var CountryInterface $country */
$country = $countryFactory->create();
$data = [
    CountryInterface::COUNTRY_ID => 100,
    CountryInterface::ENABLED => true,
    CountryInterface::POSITION => 100,
    CountryInterface::NAME => 'Country-name-100',
];
$dataObjectHelper->populateWithArray($country, $data, CountryInterface::class);
$countryRepository->save($country);
