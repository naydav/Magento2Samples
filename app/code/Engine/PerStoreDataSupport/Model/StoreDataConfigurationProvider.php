<?php
namespace Engine\PerStoreDataSupport\Model;

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
    public function get($interfaceName)
    {
        if (!isset(
            $this->configuration[$interfaceName],
            $this->configuration[$interfaceName][StoreDataConfiguration::FIELDS],
            $this->configuration[$interfaceName][StoreDataConfiguration::REFERENCE_FIELD],
            $this->configuration[$interfaceName][StoreDataConfiguration::STORE_DATA_TABLE]
        )) {
            throw new LocalizedException(__('Invalid configuration for "%1".', $interfaceName));
        }
        $configurationData = $this->configuration[$interfaceName];

        $configuration = $this->storeDataConfigurationBuilder
            ->setFields($configurationData[StoreDataConfiguration::FIELDS])
            ->setReferenceField($configurationData[StoreDataConfiguration::REFERENCE_FIELD])
            ->setStoreDataTable($configurationData[StoreDataConfiguration::STORE_DATA_TABLE])
            ->create();
        return $configuration;
    }
}
