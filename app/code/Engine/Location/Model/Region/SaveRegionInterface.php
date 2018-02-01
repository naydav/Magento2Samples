<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;

/**
 * Save Region data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Engine\Location\Api\RegionRepositoryInterface
 * @api
 */
interface SaveRegionInterface
{
    /**
     * Save Region data
     *
     * @param RegionInterface $region
     * @return int
     * @throws ValidationException
     * @throws CouldNotSaveException
     */
    public function execute(RegionInterface $region): int;
}
