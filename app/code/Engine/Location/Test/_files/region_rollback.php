<?php
declare(strict_types=1);

use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
try {
    $regionRepository->deleteById(100);
} catch (NoSuchEntityException $e) {
    // Tests which are wrapped with MySQL transaction clear all data by transaction rollback.
}
