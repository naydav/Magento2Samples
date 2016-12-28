<?php
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityInterfaceFactory $cityFactory */
$cityFactory = Bootstrap::getObjectManager()->get(CityInterfaceFactory::class);
/** @var HydratorInterface $hydrator */
$hydrator = Bootstrap::getObjectManager()->get(HydratorInterface::class);
/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);

require '../../../app/code/Engine/Location/Test/_files/region/region.php';

/** @var CityInterface $city */
$city = $cityFactory->create();
$city = $hydrator->hydrate($city, [
    CityInterface::CITY_ID => 100,
    CityInterface::REGION_ID => 100,
    CityInterface::TITLE => 'title-0',
    CityInterface::IS_ENABLED => true,
    CityInterface::POSITION => 200,
]);
$cityRepository->save($city);