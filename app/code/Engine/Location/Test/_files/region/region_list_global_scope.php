<?php
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionInterfaceFactory $regionFactory */
$regionFactory = Bootstrap::getObjectManager()->get(RegionInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);

$regionsData = [
    [
        RegionInterface::REGION_ID => 100,
        RegionInterface::TITLE => 'region-3',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 300,
    ],
    [
        RegionInterface::REGION_ID => 200,
        RegionInterface::TITLE => 'region-2',
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 200,
    ],
    [
        RegionInterface::REGION_ID => 300,
        RegionInterface::TITLE => 'region-2',
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 200,
    ],
    [
        RegionInterface::REGION_ID => 400,
        RegionInterface::TITLE => 'region-1',
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 100,
    ],
];
foreach ($regionsData as $regionData) {
    /** @var RegionInterface $region */
    $region = $regionFactory->create();
    $region = $hydrator->hydrate($region, $regionData);
    $regionRepository->save($region);
}
