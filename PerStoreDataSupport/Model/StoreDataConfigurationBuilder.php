<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterface;
use Magento\Framework\Api\AbstractSimpleObjectBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method StoreDataConfigurationInterface create()
 */
class StoreDataConfigurationBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->_set(StoreDataConfigurationInterface::FIELDS, $fields);
        return $this;
    }

    /**
     * @param string $storeDataTable
     * @return $this
     */
    public function setStoreDataTable($storeDataTable)
    {
        $this->_set(StoreDataConfigurationInterface::STORE_DATA_TABLE, $storeDataTable);
        return $this;
    }

    /**
     * @param string $referenceField
     * @return $this
     */
    public function setReferenceField($referenceField)
    {
        $this->_set(StoreDataConfigurationInterface::REFERENCE_FIELD, $referenceField);
        return $this;
    }
}
