<?php
namespace Engine\Backend\Api\Ui\DataProvider;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
interface StoreMetaModifierInterface
{
    /**
     * @param array $meta
     * @param array $perStoreFields
     * @param Object $entityInGlobalScope
     * @param Object $entityInCurrentScope
     * @return array
     */
    public function modify(array $meta, array $perStoreFields, $entityInGlobalScope, $entityInCurrentScope);
}
