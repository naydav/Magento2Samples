<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\PerStoreDataSupport\Api\DataProviderMetaModifierInterface;
use Engine\PerStoreDataSupport\Api\DataProviderSearchResultFactoryInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
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
class RegionDataProvider extends DataProvider
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
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

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
     * @param RegionCollectionFactory $regionCollectionFactory
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
        RegionCollectionFactory $regionCollectionFactory,
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
        $this->regionCollectionFactory = $regionCollectionFactory;
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
        return $configData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $regionId = $this->request->getParam('region_id');
        if (null !== $regionId) {
            $meta = $this->dataProviderMetaModifier->modify(RegionInterface::class, $regionId, $meta);
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        /** @var RegionCollection $collection */
        $collection = $this->regionCollectionFactory->create();
        $collection->addStoreData();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResult = $this->dataProviderSearchResultFactory->create(
            $collection->getItems(),
            $collection->getSize(),
            $searchCriteria,
            RegionInterface::REGION_ID
        );
        return $searchResult;
    }
}
