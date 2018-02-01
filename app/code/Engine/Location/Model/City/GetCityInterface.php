<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get City by cityId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CityRepositoryInterface
 * @api
 */
interface GetCityInterface
{
    /**
     * Get City data by given cityId
     *
     * @param int $cityId
     * @return CityInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $cityId): CityInterface;
}
