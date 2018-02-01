<?php
declare(strict_types=1);

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityInterfaceFactory $cityFactory */
$cityFactory = Bootstrap::getObjectManager()->get(CityInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);

/** @var CityInterface $city */
$city = $cityFactory->create();
$data = [
    CityInterface::CITY_ID => 100,
    CityInterface::REGION_ID => 100,
    CityInterface::ENABLED => true,
    CityInterface::POSITION => 100,
    CityInterface::NAME => 'City-name-100',
];
$dataObjectHelper->populateWithArray($city, $data, CityInterface::class);
$cityRepository->save($city);
