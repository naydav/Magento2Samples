<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataLoader
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @param ResourceConnection $resourceConnection
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
    }

    /**
     * @param string $interfaceName
     * @param int $entityId
     * @param int $storeId
     * @return array|null
     */
    public function load($interfaceName, $entityId, $storeId)
    {
        $connection = $this->resourceConnection->getConnection();
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($interfaceName);
        $storeDataTable = $connection->getTableName($storeDataConfiguration->getStoreDataTable());
        $referenceField = $storeDataConfiguration->getReferenceField();
        $fields = $storeDataConfiguration->getFields();

        $select = $connection->select()
            ->from($storeDataTable, $fields)
            ->where('store_id = ?', (int)$storeId)
            ->where("{$referenceField} = ?", (int)$entityId);
        $result = $connection->fetchRow($select);
        return ($result === false) ? null : $result;
    }
}
