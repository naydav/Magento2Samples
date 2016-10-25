<?php
namespace Engine\Backend\Api;

use Magento\Store\Api\Data\StoreInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
interface StoreContextInterface
{
    /**
     * @return StoreInterface
     */
    public function getCurrentStore();

    /**
     * @param $storeId
     * @return void
     */
    public function setCurrentStoreById($storeId);
}
