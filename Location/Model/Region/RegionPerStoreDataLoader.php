<?php
namespace Engine\Location\Model\Region;

use Magento\Framework\App\ResourceConnection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionPerStoreDataLoader
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @param ResourceConnection $resourceConnection
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
    }

    /**
     * @param int $regionId
     * @param int $storeId
     * @return array|null
     */
    public function load($regionId, $storeId)
    {
        $connection = $this->resourceConnection->getConnection();
        $regionStoreTable = $connection->getTableName('engine_location_region_store');
        $fields = $this->regionPerStoreFieldsProvider->getFields();

        $select = $connection->select()
            ->from($regionStoreTable, $fields)
            ->where('store_id = ?', (int)$storeId)
            ->where('region_id = ?', (int)$regionId);
        $result = $connection->fetchRow($select);
        return ($result === false) ? null : $result;
    }
}
