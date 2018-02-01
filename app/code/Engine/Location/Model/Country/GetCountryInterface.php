<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get Country by countryId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CountryRepositoryInterface
 * @api
 */
interface GetCountryInterface
{
    /**
     * Get Country data by given countryId
     *
     * @param int $countryId
     * @return CountryInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $countryId): CountryInterface;
}
