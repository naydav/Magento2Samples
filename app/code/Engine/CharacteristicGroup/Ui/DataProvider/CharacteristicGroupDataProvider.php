<?php
namespace Engine\CharacteristicGroup\Ui\DataProvider;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\PerStoreDataSupport\Api\DataProviderMetaModifierInterface;
use Engine\PerStoreDataSupport\Api\DataProviderSearchResultFactoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class CharacteristicGroupDataProvider extends DataProvider
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataProviderMetaModifierInterface
     */
    private $dataProviderMetaModifier;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var DataProviderSearchResultFactoryInterface
     */
    private $dataProviderSearchResultFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param DataProviderMetaModifierInterface $dataProviderMetaModifier
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param DataProviderSearchResultFactoryInterface $dataProviderSearchResultFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        DataProviderMetaModifierInterface $dataProviderMetaModifier,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        DataProviderSearchResultFactoryInterface $dataProviderSearchResultFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->dataProviderMetaModifier = $dataProviderMetaModifier;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->dataProviderSearchResultFactory = $dataProviderSearchResultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $configData = parent::getConfigData();
        $storeId = $this->storeManager->getStore()->getId();

        $configData['submit_url'] = $this->urlBuilder->getUrl(
            'engine_characteristic_group/characteristicGroup/save',
            [
                'store' => $storeId,
            ]
        );
        $configData['validate_url'] = $this->urlBuilder->getUrl(
            'engine_characteristic_group/characteristicGroup/validate',
            [
                'store' => $storeId,
            ]
        );
        $configData['update_url'] = $this->urlBuilder->getUrl('mui/index/render', [
            'store' => $storeId,
        ]);
        return $configData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        if ('engine_characteristic_group_form_data_source' === $this->name) {
            $characteristicGroupId = $this->request->getParam(CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID);
            if (null !== $characteristicGroupId) {
                $meta = $this->dataProviderMetaModifier->modify(
                    CharacteristicGroupInterface::class,
                    $characteristicGroupId,
                    $meta
                );
            }
        }

        if ('engine_characteristic_group_listing_data_source' === $this->name) {
            $storeId = $this->storeManager->getStore()->getId();
            $inlineEditUrl = $this->urlBuilder->getUrl('engine_characteristic_group/characteristicGroup/inlineEdit', [
                'store' => $storeId,
            ]);
            $meta['characteristic_group_columns']['arguments']['data']['config']['editorConfig']['clientConfig']
            ['saveUrl'] = $inlineEditUrl;
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        if ('engine_characteristic_group_form_data_source' === $this->name) {
            // It is need for support several fieldsets. For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $characteristicGroupId = $data['items'][0][CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID];
                $dataForSingle[$characteristicGroupId] = [
                    'general' => $data['items'][0],
                ];
                $data = $dataForSingle;
            } else {
                $data = [];
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->characteristicGroupRepository->getList($searchCriteria);

        $searchResult = $this->dataProviderSearchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
        );
        return $searchResult;
    }
}
