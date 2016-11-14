<?php
namespace Engine\Location\Model\City\ResourceModel;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CityResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('engine_location_city', CityInterface::CITY_ID);
    }
}
