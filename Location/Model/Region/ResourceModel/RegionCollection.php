<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Model\Region\Region;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionCollection extends AbstractCollection
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @var bool
     */
    private $isStoreDataAdded = false;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
    }


    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Region::class, RegionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldName()
    {
        return $this->getResource()->getIdFieldName();
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fields = is_array($field) ? $field : [$field];
        $perStoreFields = $this->regionPerStoreFieldsProvider->getFields();

        foreach ($fields as &$field) {
            if (in_array($field, $perStoreFields, true)) {
                $this->addStoreData();
                $field = $this->resolveField($field);
            }
        }
        unset($field);

        return parent::addFieldToFilter($fields, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $perStoreFields = $this->regionPerStoreFieldsProvider->getFields();
        if (in_array($field, $perStoreFields, true)) {
            $this->addStoreData();
            $field = $this->resolveField($field);
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $perStoreFields = $this->regionPerStoreFieldsProvider->getFields();
        if (in_array($field, $perStoreFields, true)) {
            $this->addStoreData();
            $field = $this->resolveField($field);
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * @param string $field
     * @return string|\Zend_Db_Expr
     */
    private function resolveField($field)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        if (Store::DEFAULT_STORE_ID === $storeId) {
            $field = "global_scope.$field";
        } else {
            $field = $this->getConnection()->getIfNullSql(
                "store_scope.`{$field}`", "global_scope.`{$field}`"
            );
        }
        return $field;
    }

    /**
     * @return self
     */
    public function addStoreData()
    {
        if (false === $this->isStoreDataAdded) {
            $storeId = (int)$this->storeManager->getStore()->getId();
            $select = $this->getSelect();
            $regionStoreTable = $this->getTable('engine_location_region_store');
            $perStoreFields = $this->regionPerStoreFieldsProvider->getFields();

            $columns = [];
            foreach ($perStoreFields as $field) {
                $columns[$field] = $this->resolveField($field);
            }

            if (Store::DEFAULT_STORE_ID === $storeId) {
                $select
                    ->joinLeft(
                        ['global_scope' => $regionStoreTable],
                        'main_table.region_id = global_scope.region_id AND global_scope.store_id = '
                        . Store::DEFAULT_STORE_ID,
                        $columns
                    );
            } else {
                $select->columns($columns)
                    ->joinLeft(
                        ['global_scope' => $regionStoreTable],
                        'main_table.region_id = global_scope.region_id AND global_scope.store_id = '
                        . Store::DEFAULT_STORE_ID,
                        null
                    )
                    ->joinLeft(
                        ['store_scope' => $regionStoreTable],
                        'main_table.region_id = store_scope.region_id AND store_scope.store_id = ' . $storeId,
                        null
                    );
            }
            $this->isStoreDataAdded = true;
        }
        return $this;
    }
}
