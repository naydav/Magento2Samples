<?php
namespace Engine\PerStoreDataSupport\Api;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface DataProviderMetaModifierInterface
{
    /**
     * @param string $interfaceName
     * @param int $entityId
     * @param array $meta
     * @return array
     */
    public function modify($interfaceName, $entityId, array $meta);
}
