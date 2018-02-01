<?php
namespace Engine\Location\Model\City\ResourceModel;

use Engine\Location\Model\City\City as CityModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CityCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CityModel::class, City::class);
    }
}
