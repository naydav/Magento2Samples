<?php
namespace Engine\PerStoreDataSupport\Ui\DataProvider;

use Engine\PerStoreDataSupport\Api\DataProviderMetaModifierInterface;
use Engine\PerStoreDataSupport\Api\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Model\StoreDataLoader;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class DataProviderMetaModifier implements DataProviderMetaModifierInterface
{
    /**
     * @var StoreDataConfigurationProviderInterface
     */
    private $storeDataConfigurationProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreDataLoader
     */
    private $storeDataLoader;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var string
     */
    private $helperServiceTemplate;

    /**
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     * @param StoreManagerInterface $storeManager
     * @param StoreDataLoader $storeDataLoader
     * @param ArrayManager $arrayManager
     * @param string $helperServiceTemplate
     */
    public function __construct(
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider,
        StoreManagerInterface $storeManager,
        StoreDataLoader $storeDataLoader,
        ArrayManager $arrayManager,
        $helperServiceTemplate = 'ui/form/element/helper/service'
    ) {
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
        $this->storeManager = $storeManager;
        $this->storeDataLoader = $storeDataLoader;
        $this->arrayManager = $arrayManager;
        $this->helperServiceTemplate = $helperServiceTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($interfaceName, $entityId, array $meta)
    {
        $storeId = $this->storeManager->getStore()->getId();

        if (Store::DEFAULT_STORE_ID !== (int)$storeId) {
            $dataInGlobalScope = $this->storeDataLoader->load(
                $interfaceName,
                $entityId,
                Store::DEFAULT_STORE_ID
            );
            $dataInCurrentScope = $this->storeDataLoader->load(
                $interfaceName,
                $entityId,
                $storeId
            );
            $meta = $this->modifyFieldsMeta(
                $interfaceName,
                $meta,
                $dataInGlobalScope,
                $dataInCurrentScope
            );
        }
        return $meta;
    }

    /**
     * @param string $interfaceName
     * @param array $meta
     * @param $dataInGlobalScope
     * @param $dataInCurrentScope
     * @return array
     */
    private function modifyFieldsMeta(
        $interfaceName,
        array $meta,
        array $dataInGlobalScope,
        array $dataInCurrentScope = null
    ) {
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($interfaceName);
        $fields = $storeDataConfiguration->getFields();

        foreach ($fields as $field) {
            $elementPath = $this->arrayManager->findPath($field, $meta);

            $config = (null !== $dataInCurrentScope) && isset($dataInCurrentScope[$field])
                ? [
                    'default' => $dataInCurrentScope[$field],
                    'disabled' => false,
                ]
                : [
                    'default' => $dataInGlobalScope[$field],
                    'disabled' => true,
                ];
            $config['service']['template'] = $this->helperServiceTemplate;

            if (null !== $elementPath) {
                $meta = $this->arrayManager->merge($elementPath . '/arguments/data/config', $meta, $config);
            }
        }
        return $meta;
    }
}
