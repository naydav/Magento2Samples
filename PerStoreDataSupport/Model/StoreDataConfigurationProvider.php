<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterface;
use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterfaceFactory;
use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataConfigurationProvider implements StoreDataConfigurationProviderInterface
{
    /**
     * @var StoreDataConfigurationInterfaceFactory
     */
    private $configurationFactory;

    /**
     * Key is entity interface, value is metadata
     *
     * @var array
     */
    private $configuration;

    /**
     * @param StoreDataConfigurationInterfaceFactory $configurationFactory
     * @param array $configuration
     */
    public function __construct(
        StoreDataConfigurationInterfaceFactory $configurationFactory,
        array $configuration
    ) {
        $this->configurationFactory = $configurationFactory;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function provide($interfaceName)
    {
        if (!isset(
            $this->configuration[$interfaceName],
            $this->configuration[$interfaceName][StoreDataConfigurationInterface::FIELDS],
            $this->configuration[$interfaceName][StoreDataConfigurationInterface::REFERENCE_FIELD],
            $this->configuration[$interfaceName][StoreDataConfigurationInterface::STORE_DATA_TABLE]
        )) {
            throw new LocalizedException(__('Invalid configuration for "%1".', $interfaceName));
        }
        $configurationData = $this->configuration[$interfaceName];

        /** @var StoreDataConfigurationInterface $configuration */
        $configuration = $this->configurationFactory->create();
        $configuration->setFields($configurationData[StoreDataConfigurationInterface::FIELDS]);
        $configuration->setReferenceField($configurationData[StoreDataConfigurationInterface::REFERENCE_FIELD]);
        $configuration->setStoreDataTable($configurationData[StoreDataConfigurationInterface::STORE_DATA_TABLE]);
        return $configuration;
    }
}
