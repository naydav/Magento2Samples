<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\Backend\Api\Ui\DataProvider\StoreMetaModifierInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\RegionPerStoreFieldsProvider;
use Engine\Location\Model\Region\RegionPerStoreDataLoader;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\DocumentFactory;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\Store;
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
     * @var RegionPerStoreDataLoader
     */
    private $regionPerStoreDataLoader;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreMetaModifierInterface
     */
    private $storeMetaModifier;

    /**
     * @var RegionPerStoreFieldsProvider
     */
    private $regionPerStoreFieldsProvider;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionPerStoreDataLoader
     */
    private $regionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var DocumentFactory
     */
    private $documentFactory;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var AttributeValueFactory
     */
    private $attributeValueFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param UrlInterface $urlBuilder
     * @param RegionPerStoreDataLoader $regionPerStoreDataLoader
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param StoreMetaModifierInterface $storeMetaModifier
     * @param RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider
     * @param HydratorInterface $hydrator
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param DocumentFactory $documentFactory
     * @param SearchResultFactory $searchResultFactory
     * @param AttributeValueFactory $attributeValueFactory
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
        RegionPerStoreDataLoader $regionPerStoreDataLoader,
        Registry $registry,
        StoreManagerInterface $storeManager,
        StoreMetaModifierInterface $storeMetaModifier,
        RegionPerStoreFieldsProvider $regionPerStoreFieldsProvider,
        HydratorInterface $hydrator,
        RegionCollectionFactory $regionCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        DocumentFactory $documentFactory,
        SearchResultFactory $searchResultFactory,
        AttributeValueFactory $attributeValueFactory,
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
        $this->regionPerStoreDataLoader = $regionPerStoreDataLoader;
        $this->storeManager = $storeManager;
        $this->storeMetaModifier = $storeMetaModifier;
        $this->regionPerStoreFieldsProvider = $regionPerStoreFieldsProvider;
        $this->hydrator = $hydrator;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->documentFactory = $documentFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->attributeValueFactory = $attributeValueFactory;
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
        $storeId = $this->storeManager->getStore()->getId();

        if (Store::DEFAULT_STORE_ID !== (int)$storeId) {
            $regionId = $this->request->getParam('region_id');
            if (null !== $regionId) {
                $perStoreFields = $this->regionPerStoreFieldsProvider->getFields();
                $dataInGlobalScope = $this->regionPerStoreDataLoader->load($regionId, Store::DEFAULT_STORE_ID);
                $dataInCurrentScope = $this->regionPerStoreDataLoader->load($regionId, $storeId);
                $meta = $this->storeMetaModifier->modify(
                    $meta,
                    $perStoreFields,
                    $dataInGlobalScope,
                    $dataInCurrentScope
                );
            }
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

        $items = $collection->getItems();
        $documents = [];
        foreach ($items as $item) {
            $itemData = $this->hydrator->extract($item);
            $itemId = $itemData[RegionInterface::REGION_ID];

            $attribute = $this->attributeValueFactory->create();
            $attribute->setAttributeCode('id_field_name');
            $attribute->setValue(RegionInterface::REGION_ID);
            $attributes[] = $attribute;
            foreach ($itemData as $key => $value) {
                $attribute = $this->attributeValueFactory->create();
                $attribute->setAttributeCode($key);
                if (!is_array($value)) {
                    $value = (string)$value;
                }
                $attribute->setValue($value);
                $attributes[] = $attribute;
            }

            $document = $this->documentFactory->create();
            $document->setId($itemId);
            $document->setCustomAttributes($attributes);
            $documents[] = $document;
        }

        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($documents);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
