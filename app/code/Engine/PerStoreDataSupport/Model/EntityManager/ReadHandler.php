<?php
namespace Engine\PerStoreDataSupport\Model\EntityManager;

use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Api\StoreDataSelectProcessorInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class ReadHandler implements ExtensionInterface
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
     * @var StoreDataSelectProcessorInterface
     */
    private $storeDataSelectProcessor;

    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @param string $interfaceName
     * @param ResourceConnection $resourceConnection
     * @param HydratorInterface $hydrator
     * @param StoreDataSelectProcessorInterface $storeDataSelectProcessor
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     */
    public function __construct(
        $interfaceName,
        ResourceConnection $resourceConnection,
        HydratorInterface $hydrator,
        StoreDataSelectProcessorInterface $storeDataSelectProcessor,
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
    ) {
        $this->interfaceName = $interfaceName;
        $this->resourceConnection = $resourceConnection;
        $this->hydrator = $hydrator;
        $this->storeDataSelectProcessor = $storeDataSelectProcessor;
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
        $referenceField = $storeDataConfiguration->getReferenceField();

        $entityData = $this->hydrator->extract($entity);
        $select = $this->storeDataSelectProcessor->processGetStoreData(
            $this->interfaceName,
            $connection->select(),
            $entityData[$referenceField]
        );

        $result = $connection->fetchRow($select);
        if (false !== $result) {
            $entity = $this->hydrator->hydrate($entity, $result);
        }
        return $entity;
    }
}
