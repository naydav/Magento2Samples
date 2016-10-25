<?php
namespace Engine\Backend\Ui\DataProvider;

use Engine\Backend\Api\Ui\DataProvider\StoreMetaModifierInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreMetaModifier implements StoreMetaModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var string
     */
    private $helperServiceTemplate;

    /**
     * @param ArrayManager $arrayManager
     * @param string $helperServiceTemplate
     */
    public function __construct(
        ArrayManager $arrayManager,
        $helperServiceTemplate = 'ui/form/element/helper/service'
    ) {
        $this->arrayManager = $arrayManager;
        $this->helperServiceTemplate = $helperServiceTemplate;
    }

    /**
     * @param array $meta
     * @param array $perStoreFields
     * @param array $dataInGlobalScope
     * @param array $dataInCurrentScope
     * @return array
     */
    public function modify(array $meta, array $perStoreFields, array $dataInGlobalScope, array $dataInCurrentScope)
    {
        foreach ($perStoreFields as $field) {
            $elementPath = $this->arrayManager->findPath($field, $meta);

            $config = isset($dataInCurrentScope[$field])
                ? [
                    'default' => $dataInCurrentScope[$field],
                    'disabled' => false,
                ]
                : [
                    'default' => $dataInGlobalScope[$field],
                    'disabled' => true,
                ];
            $config['service']['template'] = $this->helperServiceTemplate;

            if (null === $elementPath) {
                $meta['general']['children'][$field]['arguments']['data']['config'] = $config;
            } else {
                $meta = $this->arrayManager->merge($elementPath . '/arguments/data/config', $meta, $config);
            }
        }
        return $meta;
    }
}
