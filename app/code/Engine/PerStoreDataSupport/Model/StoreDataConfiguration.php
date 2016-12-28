<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataConfiguration extends AbstractSimpleObject implements StoreDataConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->_get(self::FIELDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setFields(array $fields)
    {
        $this->setData(self::FIELDS, $fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreDataTable()
    {
        return $this->_get(self::STORE_DATA_TABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreDataTable($storeDataTable)
    {
        $this->setData(self::STORE_DATA_TABLE, $storeDataTable);
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceField()
    {
        return $this->_get(self::REFERENCE_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceField($referenceField)
    {
        $this->setData(self::REFERENCE_FIELD, $referenceField);
    }
}
