<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Api\StoreDataSelectProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataSelectProcessor implements StoreDataSelectProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resourceConnection
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ResourceConnection $resourceConnection,
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
    ) {
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function processAddStoreData($interfaceName, Select $select)
    {
        $select = clone $select;
        $storeId = (int)$this->storeManager->getStore()->getId();
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($interfaceName);
        $storeDataTable = $this->resourceConnection->getConnection()->getTableName(
            $storeDataConfiguration->getStoreDataTable()
        );
        $referenceField = $storeDataConfiguration->getReferenceField();
        $fields = $storeDataConfiguration->getFields();

        $columns = [];
        foreach ($fields as $field) {
            $columns[$field] = $this->resolveField($field);
        }

        if (Store::DEFAULT_STORE_ID === $storeId) {
            $select
                ->joinLeft(
                    ['global_scope' => $storeDataTable],
                    "main_table.{$referenceField} = global_scope.{$referenceField} AND global_scope.store_id = "
                    . Store::DEFAULT_STORE_ID,
                    $columns
                );
        } else {
            $select->columns($columns)
                ->joinLeft(
                    ['global_scope' => $storeDataTable],
                    "main_table.{$referenceField} = global_scope.{$referenceField} AND global_scope.store_id = "
                    . Store::DEFAULT_STORE_ID,
                    null
                )
                ->joinLeft(
                    ['store_scope' => $storeDataTable],
                    "main_table.{$referenceField} = store_scope.{$referenceField} AND store_scope.store_id = "
                    . $storeId,
                    null
                );
        }
        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function processGetStoreData($interfaceName, Select $select, $entityId)
    {
        $select = clone $select;
        $storeId = (int)$this->storeManager->getStore()->getId();
        $entityId = (int)$entityId;
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($interfaceName);
        $storeDataTable = $this->resourceConnection->getConnection()->getTableName(
            $storeDataConfiguration->getStoreDataTable()
        );
        $referenceField = $storeDataConfiguration->getReferenceField();
        $fields = $storeDataConfiguration->getFields();

        $columns = [];
        foreach ($fields as $field) {
            $columns[$field] = $this->resolveField($field);
        }
        $select
            ->from(['global_scope' => $storeDataTable], $columns)
            ->where('global_scope.store_id = ?', Store::DEFAULT_STORE_ID)
            ->where("global_scope.{$referenceField} = ?", $entityId);

        if (Store::DEFAULT_STORE_ID !== $storeId) {
            $select
                ->joinLeft(
                    ['store_scope' => $storeDataTable],
                    "store_scope.{$referenceField} = global_scope.{$referenceField} AND store_scope.store_id = "
                    . $storeId,
                    null
                );
        }
        return $select;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveField($field)
    {
        $storeId = (int)$this->storeManager->getStore()->getId();

        if (Store::DEFAULT_STORE_ID === $storeId) {
            $field = "global_scope.{$field}";
        } else {
            $field = $this->resourceConnection->getConnection()->getIfNullSql(
                "store_scope.`{$field}`", "global_scope.`{$field}`"
            );
        }
        return $field;
    }
}
