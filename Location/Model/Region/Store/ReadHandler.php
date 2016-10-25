<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\DataRegionHelper;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;

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
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var DataRegionHelper
     */
    private $dataRegionHelper;

    /**
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreContextInterface $storeContext
     * @param DataRegionHelper $dataRegionHelper
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreContextInterface $storeContext,
        DataRegionHelper $dataRegionHelper,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeContext = $storeContext;
        $this->dataRegionHelper = $dataRegionHelper;
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
        $storeId = $this->storeContext->getCurrentStore()->getId();
        $select = $connection->select()
            ->from(
                $connection->getTableName('engine_location_region_store'),
                $this->regionPerStoreFieldsProvider->getFields()
            )
            ->where('store_id = ?', $storeId)
            ->where(RegionInterface::REGION_ID . ' = ?', $region->getRegionId());
        $result = $connection->fetchRow($select);
        if (false !== $result) {
            $this->dataRegionHelper->populateWithArray($region, $result);
        }
        return $region;
    }
}
