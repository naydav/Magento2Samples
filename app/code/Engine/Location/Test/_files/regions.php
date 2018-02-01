<?php
declare(strict_types=1);

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var DataObjectHelper $dataObjectHelper */
$dataObjectHelper = Bootstrap::getObjectManager()->get(DataObjectHelper::class);
/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);

$regionsData = [
    [
        RegionInterface::REGION_ID => 100,
        RegionInterface::COUNTRY_ID => 300,
        RegionInterface::ENABLED => true,
        RegionInterface::POSITION => 300,
        RegionInterface::NAME => 'Region-name-1',
    ],
    [
        RegionInterface::REGION_ID => 200,
        RegionInterface::COUNTRY_ID => 200,
        RegionInterface::ENABLED => true,
        RegionInterface::POSITION => 200,
        RegionInterface::NAME => 'Region-name-2',
    ],
    [
        RegionInterface::REGION_ID => 300,
        RegionInterface::COUNTRY_ID => 200,
        RegionInterface::ENABLED => false,
        RegionInterface::POSITION => 200,
        RegionInterface::NAME => 'Region-name-2',
    ],
    [
        RegionInterface::REGION_ID => 400,
        RegionInterface::COUNTRY_ID => 100,
        RegionInterface::ENABLED => false,
        RegionInterface::POSITION => 100,
        RegionInterface::NAME => 'Region-name-3',
    ],
];
foreach ($regionsData as $regionData) {
    /** @var RegionInterface $region */
    $region = $regionFactory->create();
    $dataObjectHelper->populateWithArray($region, $regionData, RegionInterface::class);
    $regionRepository->save($region);
}
