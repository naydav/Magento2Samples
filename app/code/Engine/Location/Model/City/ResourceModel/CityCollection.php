<?php
namespace Engine\Location\Model\City\ResourceModel;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\City;
use Engine\PerStoreDataSupport\Model\ResourceModel\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CityCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(City::class, CityResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaceName()
    {
        return CityInterface::class;
    }
}
