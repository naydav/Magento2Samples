<?php
namespace Engine\PerStoreDataSupport\Api\Data;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
interface StoreDataConfigurationInterface
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
    public function getFields();

    /**
     * @param array $fields
     * @return void
     */
    public function setFields(array $fields);

    /**
     * @return string
     */
    public function getStoreDataTable();

    /**
     * @param string $storeDataTable
     * @return void
     */
    public function setStoreDataTable($storeDataTable);

    /**
     * @return string
     */
    public function getReferenceField();

    /**
     * @param string $referenceField
     * @return void
     */
    public function setReferenceField($referenceField);
}
