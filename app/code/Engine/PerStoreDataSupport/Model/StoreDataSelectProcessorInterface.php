<?php
namespace Engine\PerStoreDataSupport\Model;

use Magento\Framework\DB\Select;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @spi
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
