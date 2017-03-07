<?php
namespace Engine\PerStoreDataSupport\Model\ResourceModel;

use Engine\PerStoreDataSupport\Model\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Model\StoreDataSelectProcessorInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as BaseAbstarctCollection;
use Psr\Log\LoggerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
abstract class AbstractCollection extends BaseAbstarctCollection
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
     * @return string
     */
    abstract protected function getInterfaceName();

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
    public function addFilter($field, $value, $type = 'and')
    {
        $field = $this->processField($field);
        return parent::addFilter($field, $value, $type);
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
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($this->getInterfaceName());
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
                $this->getInterfaceName(),
                $this->getSelect()
            );
            $this->isStoreDataAdded = true;
        }
        return $this;
    }
}
