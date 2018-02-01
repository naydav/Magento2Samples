<?php
declare(strict_types=1);

use Engine\Location\Api\CountryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/** @var CountryRepositoryInterface $countryRepository */
$countryRepository = Bootstrap::getObjectManager()->get(CountryRepositoryInterface::class);
try {
    $countryRepository->deleteById(100);
} catch (NoSuchEntityException $e) {
    // Tests which are wrapped with MySQL transaction clear all data by transaction rollback.
}
