<?php
namespace Engine\PerStoreDataSupport\Model\EntityManager;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\Store;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @spi
 */
class CreateHandler implements ExtensionInterface
{
    /**
     * @var string
     */
    private $interfaceName;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @param string $interfaceName
     * @param ResourceConnection $resourceConnection
     * @param HydratorInterface $hydrator
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     */
    public function __construct(
        $interfaceName,
        ResourceConnection $resourceConnection,
        HydratorInterface $hydrator,
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
    ) {
        $this->interfaceName = $interfaceName;
        $this->resourceConnection = $resourceConnection;
        $this->hydrator = $hydrator;
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $connection = $this->resourceConnection->getConnection();
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($this->interfaceName);
        $storeDataTable = $connection->getTableName($storeDataConfiguration->getStoreDataTable());
        $referenceField = $storeDataConfiguration->getReferenceField();
        $fields = $storeDataConfiguration->getFields();

        $entityData = $this->hydrator->extract($entity);
        $storeData = array_intersect_key($entityData, array_flip($fields));
        $storeData[$referenceField] = $entityData[$referenceField];
        $storeData['store_id'] = Store::DEFAULT_STORE_ID;

        $connection->insert($storeDataTable, $storeData);
        return $entity;
    }
}
