<?php
namespace Engine\CharacteristicGroup\Ui\DataProvider;

use Engine\Characteristic\Api\CharacteristicRepositoryInterface;
use Engine\Characteristic\Api\Data\CharacteristicInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\PerStoreDataSupport\Ui\DataProvider\MetaDataBuilder;
use Engine\PerStoreDataSupport\Ui\DataProvider\SearchResultFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder as SearchSearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var MetaDataBuilder
     */
    private $metaDataBuilder;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchSearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param MetaDataBuilder $metaDataBuilder
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param SearchResultFactory $searchResultFactory
     * @param CharacteristicRepositoryInterface $characteristicRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchSearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        MetaDataBuilder $metaDataBuilder,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        SearchResultFactory $searchResultFactory,
        CharacteristicRepositoryInterface $characteristicRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
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
        $this->metaDataBuilder = $metaDataBuilder;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->characteristicRepository = $characteristicRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
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
                $fieldsMeta = $this->metaDataBuilder->build(
                    CharacteristicGroupInterface::class,
                    $characteristicGroupId
                );
                $meta['general']['children'] = (isset($meta['general']['children']))
                    ? array_replace_recursive($meta['general']['children'], $fieldsMeta)
                    : $fieldsMeta;
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
                    'characteristics' => [
                        'assigned_characteristics' => $this->getAssignedCharacteristicsData($characteristicGroupId),
                    ],
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

        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
        );
        return $searchResult;
    }

    /**
     * @param int $characteristicGroupId
     * @return array
     */
    private function getAssignedCharacteristicsData($characteristicGroupId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic_group', $characteristicGroupId)
            ->create();
        $characteristics = $this->characteristicRepository->getList($searchCriteria)->getItems();

        $assignedCharacteristicData = [];
        foreach ($characteristics as $characteristic) {
            $assignedCharacteristicData[] = [
                CharacteristicInterface::CHARACTERISTIC_ID => (string)$characteristic->getCharacteristicId(),
                CharacteristicInterface::IS_ENABLED => (int)$characteristic->getIsEnabled(),
                CharacteristicInterface::BACKEND_TITLE => $characteristic->getBackendTitle(),
                CharacteristicInterface::TITLE => $characteristic->getTitle(),
            ];
        }
        return $assignedCharacteristicData;
    }
}
