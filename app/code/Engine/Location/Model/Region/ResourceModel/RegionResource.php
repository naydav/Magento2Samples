<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('engine_location_region', RegionInterface::REGION_ID);
    }
}
