<?php
namespace Engine\PerStoreDataSupport\Model;

use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataConfiguration extends AbstractSimpleObject
{
    /**#@+
     * Constants defined for keys of data array
     */
    const FIELDS = 'fields';
    const STORE_DATA_TABLE = 'storeDataTable';
    const REFERENCE_FIELD = 'referenceField';
    /**#@-*/

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_get(self::FIELDS);
    }

    /**
     * @param array $fields
     * @return void
     */
    public function setFields(array $fields)
    {
        $this->setData(self::FIELDS, $fields);
    }

    /**
     * @return string
     */
    public function getStoreDataTable()
    {
        return $this->_get(self::STORE_DATA_TABLE);
    }

    /**
     * @param $storeDataTable
     * @return void
     */
    public function setStoreDataTable($storeDataTable)
    {
        $this->setData(self::STORE_DATA_TABLE, $storeDataTable);
    }

    /**
     * @return string
     */
    public function getReferenceField()
    {
        return $this->_get(self::REFERENCE_FIELD);
    }

    /**
     * @param $referenceField
     * @return void
     */
    public function setReferenceField($referenceField)
    {
        $this->setData(self::REFERENCE_FIELD, $referenceField);
    }
}
