<?php
namespace Engine\Location\Model\City\ResourceModel;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Api\StoreDataSelectProcessorInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\City;
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
class CityCollection extends AbstractCollection
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
        $this->_init(City::class, CityResource::class);
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
        if (is_array($field)) {
            foreach ($field as &$value) {
                $value = $this->processField($value);
            }
            unset($value);
        } else {
            $field = $this->processField($field);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $field = $this->processField($field);
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $field = $this->processField($field);
        return parent::addOrder($field, $direction);
    }

    /**
     * @param string $field
     * @return string|\Zend_Db_Expr
     */
    private function processField($field)
    {
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide(CityInterface::class);
        $perStoreFields = $storeDataConfiguration->getFields();

        if ($this->getIdFieldName() === $field) {
            $field = "main_table.{$field}";
        } elseif (in_array($field, $perStoreFields, true)) {
            $this->addStoreData();
            $field = $this->storeDataSelectProcessor->resolveField($field);
        } elseif ($storeDataConfiguration->getReferenceField() === $field) {
            $field = $this->storeDataSelectProcessor->resolveField($field);
        }
        return $field;
    }

    /**
     * @return self
     */
    public function addStoreData()
    {
        if (false === $this->isStoreDataAdded) {
            $this->_select = $this->storeDataSelectProcessor->processAddStoreData(
                CityInterface::class,
                $this->getSelect()
            );
            $this->isStoreDataAdded = true;
        }
        return $this;
    }
}
