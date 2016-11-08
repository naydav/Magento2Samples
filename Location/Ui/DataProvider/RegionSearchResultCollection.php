<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
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
     * @var array
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider,
        $mainTable = 'engine_location_region',
        $resourceModel = RegionResource::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
    }
}
