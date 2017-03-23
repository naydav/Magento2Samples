<?php
namespace Engine\PerStoreDataSupport\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
 */
interface StoreDataConfigurationProviderInterface
{
    /**
     * @param string $interfaceName
     * @return StoreDataConfiguration
     * @throws LocalizedException
     */
    public function get($interfaceName);
}
