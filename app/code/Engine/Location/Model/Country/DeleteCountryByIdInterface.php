<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Delete Country by countryId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Delete call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CountryRepositoryInterface
 * @api
 */
interface DeleteCountryByIdInterface
{
    /**
     * Delete the Country data by countryId
     *
     * @param int $countryId
     * @return void
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function execute(int $countryId);
}
