<?php
namespace Engine\Location\Model\Region\Store;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionHydrator;
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
     * @var RegionHydrator
     */
    private $regionHydrator;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreContextInterface $storeContext
     * @param RegionHydrator $regionHydrator
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreContextInterface $storeContext,
        RegionHydrator $regionHydrator
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeContext = $storeContext;
        $this->regionHydrator = $regionHydrator;
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
            ->from($connection->getTableName('engine_location_region_store'), [RegionInterface::TITLE])
            ->where('store_id = ?', $storeId)
            ->where(RegionInterface::REGION_ID . ' = ?', $region->getRegionId());
        $result = $connection->fetchRow($select);
        if (false !== $result) {
            $this->regionHydrator->hydrate($region, $result);
        }
        return $region;
    }
}
