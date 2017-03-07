<?php
namespace Engine\PerStoreDataSupport\Ui\DataProvider;

use Engine\PerStoreDataSupport\Model\StoreDataConfigurationProviderInterface;
use Engine\PerStoreDataSupport\Model\StoreDataLoader;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class MetaDataBuilder
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
     * @var string
     */
    private $helperServiceTemplate;

    /**
     * @param StoreDataConfigurationProviderInterface $storeDataConfigurationProvider
     * @param StoreManagerInterface $storeManager
     * @param StoreDataLoader $storeDataLoader
     * @param string $helperServiceTemplate
     */
    public function __construct(
        StoreDataConfigurationProviderInterface $storeDataConfigurationProvider,
        StoreManagerInterface $storeManager,
        StoreDataLoader $storeDataLoader,
        $helperServiceTemplate = 'ui/form/element/helper/service'
    ) {
        $this->storeDataConfigurationProvider = $storeDataConfigurationProvider;
        $this->storeManager = $storeManager;
        $this->storeDataLoader = $storeDataLoader;
        $this->helperServiceTemplate = $helperServiceTemplate;
    }

    /**
     * @param string $interfaceName
     * @param int $entityId
     * @return array
     */
    public function build($interfaceName, $entityId)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $meta = [];

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
                $dataInGlobalScope,
                $dataInCurrentScope
            );
        }
        return $meta;
    }

    /**
     * @param string $interfaceName
     * @param $dataInGlobalScope
     * @param $dataInCurrentScope
     * @return array
     */
    private function modifyFieldsMeta(
        $interfaceName,
        array $dataInGlobalScope,
        array $dataInCurrentScope = null
    ) {
        $storeDataConfiguration = $this->storeDataConfigurationProvider->provide($interfaceName);
        $fields = $storeDataConfiguration->getFields();
        $meta = [];

        foreach ($fields as $field) {
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
            $meta[$field]['arguments']['data']['config'] = $config;
        }
        return $meta;
    }
}
