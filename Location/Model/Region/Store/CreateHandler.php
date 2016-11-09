<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CreateHandler implements ExtensionInterface
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
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param ResourceConnection $resourceConnection
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider,
        HydratorInterface $hydrator
    ) {
        $this->resourceConnection = $resourceConnection;
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
        $regionPerStoreFields = $this->regionPerStoreFieldsProvider->getFields();
        $entityData = $this->hydrator->extract($region);
        $storeData = array_intersect_key($entityData, array_flip($regionPerStoreFields));
        $storeData[RegionInterface::REGION_ID] = $region->getRegionId();
        $storeData['store_id'] = Store::DEFAULT_STORE_ID;

        $connection = $this->resourceConnection->getConnection();
        $regionStoreTable = $connection->getTableName('engine_location_region_store');
        $connection->insert($regionStoreTable, $storeData);
        return $region;
    }
}
