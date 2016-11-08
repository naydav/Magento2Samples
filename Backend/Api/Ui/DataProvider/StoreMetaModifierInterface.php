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
     * @param array $dataInGlobalScope
     * @param array|null $dataInCurrentScope
     * @return array
     */
    public function modify(
        array $meta,
        array $perStoreFields,
        array $dataInGlobalScope,
        array $dataInCurrentScope = null
    );
}
