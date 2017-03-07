<?php
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var RegionRepositoryInterface $regionRepository */
$regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
try {
    $regionRepository->deleteById(300);
} catch (NoSuchEntityException $e) {
    // Region already deleted
}
