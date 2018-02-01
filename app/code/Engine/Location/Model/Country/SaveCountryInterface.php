<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;

/**
 * Save Country data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CountryRepositoryInterface
 * @api
 */
interface SaveCountryInterface
{
    /**
     * Save Country data
     *
     * @param CountryInterface $country
     * @return int
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function execute(CountryInterface $country): int;
}
