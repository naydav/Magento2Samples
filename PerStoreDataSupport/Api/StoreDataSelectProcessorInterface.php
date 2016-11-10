<?php
namespace Engine\PerStoreDataSupport\Api;

use Magento\Framework\DB\Select;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface StoreDataSelectProcessorInterface
{
    /**
     * @param string $interfaceName
     * @param Select $select
     * @return Select
     */
    public function processAddStoreData($interfaceName, Select $select);

    /**
     * @param string $interfaceName
     * @param Select $select
     * @param string $entityId
     * @return Select
     */
    public function processGetStoreData($interfaceName, Select $select, $entityId);

    /**
     * @param string $field
     * @return string|\Zend_Db_Expr
     */
    public function resolveField($field);
}
