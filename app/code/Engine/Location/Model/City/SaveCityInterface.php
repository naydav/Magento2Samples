<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;

/**
 * Save City data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\CityRepositoryInterface
 * @api
 */
interface SaveCityInterface
{
    /**
     * Save City data
     *
     * @param CityInterface $city
     * @return int
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function execute(CityInterface $city): int;
}
