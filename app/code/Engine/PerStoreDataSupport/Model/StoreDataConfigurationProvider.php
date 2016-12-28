<?php
namespace Engine\PerStoreDataSupport\Model;

use Engine\PerStoreDataSupport\Api\Data\StoreDataConfigurationInterface;
use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreDataConfigurationProvider implements StoreDataConfigurationProviderInterface
{
    /**
     * @var StoreDataConfigurationBuilder
     */
    private $storeDataConfigurationBuilder;

    /**
     * Key is entity interface, value is metadata
     *
     * @var array
     */
    private $configuration;

    /**
     * @param StoreDataConfigurationBuilder $storeDataConfigurationBuilder
     * @param array $configuration
     */
    public function __construct(
        StoreDataConfigurationBuilder $storeDataConfigurationBuilder,
        array $configuration
    ) {
        $this->storeDataConfigurationBuilder = $storeDataConfigurationBuilder;
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

        $configuration = $this->storeDataConfigurationBuilder
            ->setFields($configurationData[StoreDataConfigurationInterface::FIELDS])
            ->setReferenceField($configurationData[StoreDataConfigurationInterface::REFERENCE_FIELD])
            ->setStoreDataTable($configurationData[StoreDataConfigurationInterface::STORE_DATA_TABLE])
            ->create();
        return $configuration;
    }
}
