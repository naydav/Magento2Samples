<?php
namespace Engine\PerStoreDataSupport\Model;

use Magento\Framework\Api\AbstractSimpleObjectBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @method StoreDataConfiguration create()
 */
class StoreDataConfigurationBuilder extends AbstractSimpleObjectBuilder
{
    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->_set(StoreDataConfiguration::FIELDS, $fields);
        return $this;
    }

    /**
     * @param string $storeDataTable
     * @return $this
     */
    public function setStoreDataTable($storeDataTable)
    {
        $this->_set(StoreDataConfiguration::STORE_DATA_TABLE, $storeDataTable);
        return $this;
    }

    /**
     * @param string $referenceField
     * @return $this
     */
    public function setReferenceField($referenceField)
    {
        $this->_set(StoreDataConfiguration::REFERENCE_FIELD, $referenceField);
        return $this;
    }
}
