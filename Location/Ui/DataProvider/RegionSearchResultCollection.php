<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\Location\Model\Region\ResourceModel\RegionResource;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionSearchResultCollection extends SearchResult
{
    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'engine_location_region',
        $resourceModel = RegionResource::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @param int $storeId
     * @return void
     */
    public function addStoreData($storeId)
    {
        $regionStoreTableAlias = 'store_table';
        if (!isset($this->_joinedTables[$regionStoreTableAlias])) {
            $this->getSelect()->joinLeft(
                [$regionStoreTableAlias => $this->getTable('engine_location_region_store')],
                'store_table.region_id = main_table.region_id AND store_table.store_id = ' . (int)$storeId,
                ['title']
            );
            $this->_joinedTables[$regionStoreTableAlias] = true;
        }
    }
}
