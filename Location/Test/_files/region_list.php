<?php
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\RegionHydrator;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var RegionHydrator $regionHydrator */
$regionHydrator = Bootstrap::getObjectManager()->get(RegionHydrator::class);
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
    $region = $regionHydrator->hydrate($region, $regionData);
    $regionRepository->save($region);
}
