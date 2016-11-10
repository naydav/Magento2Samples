<?php
namespace Engine\PerStoreDataSupport\Api;

use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface StoreDataConfigurationProviderInterface
{
    /**
     * @param string $interfaceName
     * @return StoreDataConfigurationInterface
     * @throws LocalizedException
     */
    public function provide($interfaceName);
}
