<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Api\StoreDataSelectProcessorInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\Region;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionCollection extends AbstractCollection
{
    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @var StoreDataSelectProcessorInterface
     */
    private $storeDataSelectProcessor;

    /**
     * @var bool
     */
    private $isStoreDataAdded = false;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     * @param StoreDataSelectProcessorInterface $storeDataSelectProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider,
        StoreDataSelectProcessorInterface $storeDataSelectProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
        $this->storeDataSelectProcessor = $storeDataSelectProcessor;
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
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide(RegionInterface::class);
        $perStoreFields = $storeDataConfiguration->getFields();

        foreach ($fields as &$field) {
            if (in_array($field, $perStoreFields, true)) {
                $this->addStoreData();
                $field = $this->storeDataSelectProcessor->resolveField($field);
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
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide(RegionInterface::class);
        $perStoreFields = $storeDataConfiguration->getFields();
        if (in_array($field, $perStoreFields, true)) {
            $this->addStoreData();
            $field = $this->storeDataSelectProcessor->resolveField($field);
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide(RegionInterface::class);
        $perStoreFields = $storeDataConfiguration->getFields();
        if (in_array($field, $perStoreFields, true)) {
            $this->addStoreData();
            $field = $this->storeDataSelectProcessor->resolveField($field);
        }
        return parent::addOrder($field, $direction);
    }

    /**
     * @return self
     */
    public function addStoreData()
    {
        if (false === $this->isStoreDataAdded) {
            $this->_select = $this->storeDataSelectProcessor->processAddStoreData(
                RegionInterface::class,
                $this->getSelect()
            );
            $this->isStoreDataAdded = true;
        }
        return $this;
    }
}
