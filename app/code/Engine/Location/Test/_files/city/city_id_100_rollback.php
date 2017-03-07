<?php
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
try {
    $cityRepository->deleteById(100);
} catch (NoSuchEntityException $e) {
    // City already deleted
}

require '../../../app/code/Engine/Location/Test/_files/region/region_id_100_rollback.php';
