<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreContextInterface $storeContext
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeContext = $storeContext;
    }

    /**
     * @param RegionInterface $region
     * @param array $arguments
     * @return RegionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($region, $arguments = [])
    {
        $storeId = $this->storeContext->getCurrentStore()->getId();
        $storeData = [
            'store_id' => $storeId,
            RegionInterface::REGION_ID => $region->getRegionId(),
            RegionInterface::TITLE => $region->getTitle(),
        ];
        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('engine_location_region_store');
        $connection->insertOnDuplicate($table, $storeData, [RegionInterface::TITLE]);
        return $region;
    }
}
