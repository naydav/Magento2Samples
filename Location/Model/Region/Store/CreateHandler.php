<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
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
     * @param RegionInterface $region
     * @param array $arguments
     * @return RegionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($region, $arguments = [])
    {
        $storeData = [
            'store_id' => Store::DEFAULT_STORE_ID,
            RegionInterface::REGION_ID => $region->getRegionId(),
            RegionInterface::TITLE => $region->getTitle(),
        ];
        $connection = $this->resourceConnection->getConnection();
        $regionStoreTable = $connection->getTableName('engine_location_region_store');
        $connection->insert($regionStoreTable, $storeData);
        return $region;
    }
}
