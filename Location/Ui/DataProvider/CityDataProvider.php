<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\PerStoreDataSupport\Api\DataProviderMetaModifierInterface;
use Engine\PerStoreDataSupport\Api\DataProviderSearchResultFactoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\ResourceModel\CityCollection;
use Engine\Location\Model\City\ResourceModel\CityCollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CityDataProvider extends DataProvider
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
     * @var CityCollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

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
     * @param CityCollectionFactory $cityCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
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
        CityCollectionFactory $cityCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
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
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->dataProviderSearchResultFactory = $dataProviderSearchResultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $configData = parent::getConfigData();
        $storeId = $this->storeManager->getStore()->getId();

        $configData['submit_url'] = $this->urlBuilder->getUrl('*/*/save', [
            'store' => $storeId,
        ]);
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
        if ('city_form_data_source' === $this->name) {
            $cityId = $this->request->getParam('city_id');
            if (null !== $cityId) {
                $meta = $this->dataProviderMetaModifier->modify(CityInterface::class, $cityId, $meta);
            }
        }

        if ('city_listing_data_source' === $this->name) {
            $storeId = $this->storeManager->getStore()->getId();
            $inlineEditUrl = $this->urlBuilder->getUrl('*/*/inlineEdit', [
                'store' => $storeId,
            ]);
            $meta['city_columns']['arguments']['data']['config']['editorConfig']['clientConfig']['saveUrl']
                = $inlineEditUrl;
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        /** @var CityCollection $collection */
        $collection = $this->cityCollectionFactory->create();
        $collection->addStoreData();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult = $this->dataProviderSearchResultFactory->create(
            $collection->getItems(),
            $collection->getSize(),
            $searchCriteria,
            CityInterface::CITY_ID
        );
        return $searchResult;
    }
}
