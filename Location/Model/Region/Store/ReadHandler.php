<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param HydratorInterface $hydrator
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        HydratorInterface $hydrator,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->hydrator = $hydrator;
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
    }

    /**
     * @param RegionInterface $region
     * @param array $arguments
     * @return RegionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($region, $arguments = [])
    {
        $connection = $this->resourceConnection->getConnection();
        $storeId = $this->storeManager->getStore()->getId();
        $regionStoreTable = $connection->getTableName('engine_location_region_store');

        if (Store::DEFAULT_STORE_ID === $storeId) {
            $select = $connection->select()
                ->from($regionStoreTable, $this->regionPerStoreFieldsProvider->getFields())
                ->where('store_id = ?', Store::DEFAULT_STORE_ID)
                ->where('region_id = ?', (int)$region->getRegionId());
        } else {
            $columns = [];
            foreach ($this->regionPerStoreFieldsProvider->getFields() as $field) {
                $columns[$field] = $connection->getIfNullSql("store_scope.`{$field}`", "global_scope.`{$field}`");
            }
            $select = $connection->select()
                ->from(
                    ['global_scope' => $regionStoreTable],
                    $columns
                )
                ->joinLeft(
                    ['store_scope' => $regionStoreTable],
                    'store_scope.region_id = global_scope.region_id AND store_scope.store_id = ' . (int)$storeId,
                    null
                )
                ->where('global_scope.store_id = ?', Store::DEFAULT_STORE_ID)
                ->where('global_scope.region_id = ?', (int)$region->getRegionId());
        }

        $result = $connection->fetchRow($select);
        if (false !== $result) {
            $this->hydrator->hydrate($region, $result);
        }
        return $region;
    }
}
