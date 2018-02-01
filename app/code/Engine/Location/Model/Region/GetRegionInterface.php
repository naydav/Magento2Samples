<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get Region by regionId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\RegionRepositoryInterface
 * @api
 */
interface GetRegionInterface
{
    /**
     * Get Region data by given regionId
     *
     * @param int $regionId
     * @return RegionInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $regionId): RegionInterface;
}
