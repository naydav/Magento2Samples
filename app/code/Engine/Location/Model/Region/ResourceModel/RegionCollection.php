<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Model\Region\Region as RegionModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class RegionCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RegionModel::class, Region::class);
    }
}
