<?php
namespace Engine\Location\Model\Country\ResourceModel;

use Engine\Location\Model\Country\Country as CountryModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CountryCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(CountryModel::class, Country::class);
    }
}
