<?php
namespace Engine\Category\Ui\DataProvider;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\Source\GroupedCategorySource;
use Engine\PerStoreDataSupport\Ui\DataProvider\MetaDataBuilder;
use Engine\PerStoreDataSupport\Ui\DataProvider\SearchResultFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class CategoryDataProvider extends DataProvider
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
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var GroupedCategorySource
     */
    private $groupedCategorySource;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

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
     * @param MetaDataBuilder $metaDataBuilder
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchResultFactory $searchResultFactory
     * @param GroupedCategorySource $groupedCategorySource
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
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
        MetaDataBuilder $metaDataBuilder,
        CategoryRepositoryInterface $categoryRepository,
        SearchResultFactory $searchResultFactory,
        GroupedCategorySource $groupedCategorySource,
        RootCategoryIdProviderInterface $rootCategoryIdProvider,
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
        $this->categoryRepository = $categoryRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->groupedCategorySource = $groupedCategorySource;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $configData = parent::getConfigData();
        $storeId = $this->storeManager->getStore()->getId();

        $configData['submit_url'] = $this->urlBuilder->getUrl(
            'engine_category/category/save',
            [
                'store' => $storeId,
            ]
        );
        $configData['validate_url'] = $this->urlBuilder->getUrl(
            'engine_category/category/validate',
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
        if ('engine_category_form_data_source' === $this->name) {
            $categoryId = $this->request->getParam(CategoryInterface::CATEGORY_ID);
            if ($this->rootCategoryIdProvider->provide() != $categoryId) {
                $parentId = $this->request->getParam(
                    CategoryInterface::PARENT_ID,
                    $this->rootCategoryIdProvider->provide()
                );
                $meta['general']['children'][CategoryInterface::PARENT_ID]['arguments']['data'] = [
                    'options' => $this->groupedCategorySource->toOptionArray(),
                    'config' => [
                        'label' => __('Parent Id'),
                        'componentType' => Field::NAME,
                        'dataType' => Number::NAME,
                        'formElement' => Select::NAME,
                        'sortOrder' => 50,
                        'scopeLabel' => __('[GLOBAL]'),
                        'component' => 'Magento_Ui/js/form/element/ui-select',
                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                        'disableLabel' => true,
                        'filterOptions' => true,
                        'multiple' => false,
                        'validation' => [
                            'required-entry' => true,
                        ],
                        'value' => $parentId,
                    ],
                ];
            }
            if (null !== $categoryId) {
                $meta['general']['children'] = $this->metaDataBuilder->build(
                    CategoryInterface::class,
                    $categoryId
                );
            }
        }

        if ('engine_category_listing_data_source' === $this->name) {
            $storeId = $this->storeManager->getStore()->getId();
            $inlineEditUrl = $this->urlBuilder->getUrl('engine_category/category/inlineEdit', [
                'store' => $storeId,
            ]);
            $meta['category_columns']['arguments']['data']['config']['editorConfig']['clientConfig']['saveUrl'] =
                $inlineEditUrl;
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        if ('engine_category_form_data_source' === $this->name) {
            // It is need for support several fieldsets. For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $categoryId = $data['items'][0][CategoryInterface::CATEGORY_ID];
                $dataForSingle[$categoryId] = [
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
        $result = $this->categoryRepository->getList($searchCriteria);

        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            CategoryInterface::CATEGORY_ID
        );
        return $searchResult;
    }
}
