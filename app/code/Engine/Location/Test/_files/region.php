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

/** @var RegionInterface $region */
$region = $regionFactory->create();
$data = [
    RegionInterface::REGION_ID => 100,
    RegionInterface::COUNTRY_ID => 100,
    RegionInterface::ENABLED => true,
    RegionInterface::POSITION => 100,
    RegionInterface::NAME => 'Region-name-100',
];
$dataObjectHelper->populateWithArray($region, $data, RegionInterface::class);
$regionRepository->save($region);
