<?php
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\DataRegionHelper;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var DataRegionHelper $dataRegionHelper */
$dataRegionHelper = Bootstrap::getObjectManager()->get(DataRegionHelper::class);
/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);

$regions = [
    [
        RegionInterface::TITLE => 'title-1',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 100,
    ],
    [
        RegionInterface::TITLE => 'region-aa',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 200,
    ],
    [
        RegionInterface::TITLE => 'region-aa',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 200,
    ],
];
foreach ($regions as $regionData) {
    /** @var RegionInterface $region */
    $region = $regionFactory->create();
    $region = $dataRegionHelper->populateWithArray($region, $regionData);
    $regionRepository->save($region);
}
