<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class UpdateHandler implements ExtensionInterface
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
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider,
        HydratorInterface $hydrator
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
        $this->hydrator = $hydrator;
    }

    /**
     * @param RegionInterface $region
     * @param array $arguments
     * @return RegionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($region, $arguments = [])
    {
        $storeId = $this->storeManager->getStore()->getId();
        $regionPerStoreFields = $this->regionPerStoreFieldsProvider->getFields();
        $entityData = $this->hydrator->extract($region);
        $storeData = array_intersect_key($entityData, array_flip($regionPerStoreFields));
        $storeData[RegionInterface::REGION_ID] = $region->getRegionId();
        $storeData['store_id'] = $storeId;

        $connection = $this->resourceConnection->getConnection();
        $regionStoreTable = $connection->getTableName('engine_location_region_store');
        $connection->insertOnDuplicate($regionStoreTable, $storeData, $this->regionPerStoreFieldsProvider->getFields());
        return $region;
    }
}
