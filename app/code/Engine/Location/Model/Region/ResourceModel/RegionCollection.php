<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\Region;
use Engine\PerStoreDataSupport\Model\ResourceModel\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Region::class, RegionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInterfaceName()
    {
        return RegionInterface::class;
    }
}
