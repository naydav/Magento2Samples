<?php
declare(strict_types=1);

use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CityRepositoryInterface $cityRepository */
$cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
try {
    $cityRepository->deleteById(100);
} catch (NoSuchEntityException $e) {
    // Tests which are wrapped with MySQL transaction clear all data by transaction rollback.
}
