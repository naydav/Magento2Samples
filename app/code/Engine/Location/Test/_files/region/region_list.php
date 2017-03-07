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
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 300,
        RegionInterface::TITLE => 'Region-title-3',
    ],
    [
        RegionInterface::REGION_ID => 200,
        RegionInterface::IS_ENABLED => true,
        RegionInterface::POSITION => 200,
        RegionInterface::TITLE => 'Region-title-2',
    ],
    [
        RegionInterface::REGION_ID => 300,
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 200,
        RegionInterface::TITLE => 'Region-title-2',
    ],
    [
        RegionInterface::REGION_ID => 400,
        RegionInterface::IS_ENABLED => false,
        RegionInterface::POSITION => 100,
        RegionInterface::TITLE => 'Region-title-1',
    ],
];
foreach ($regionsData as $regionData) {
    /** @var RegionInterface $region */
    $region = $regionFactory->create();
    $region = $hydrator->hydrate($region, $regionData);
    $regionRepository->save($region);
}
