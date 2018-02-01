<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Delete Region by regionId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Delete call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\RegionRepositoryInterface
 * @api
 */
interface DeleteRegionByIdInterface
{
    /**
     * Delete the Region data by regionId
     *
     * @param int $regionId
     * @return void
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function execute(int $regionId);
}
