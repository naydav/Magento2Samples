<?php
namespace Engine\PerStoreDataSupport\Api\Data;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
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
     * @return string
     */
    public function getStoreDataTable();

    /**
     * @return string
     */
    public function getReferenceField();
}
