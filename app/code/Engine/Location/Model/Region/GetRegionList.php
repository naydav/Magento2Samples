<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionSearchResultInterface;
use Engine\Location\Api\Data\RegionSearchResultInterfaceFactory;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class GetRegionList implements GetRegionListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var RegionSearchResultInterfaceFactory
     */
    private $regionSearchResultFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param RegionSearchResultInterfaceFactory $regionSearchResultFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        RegionCollectionFactory $regionCollectionFactory,
        RegionSearchResultInterfaceFactory $regionSearchResultFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->regionSearchResultFactory = $regionSearchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria = null): RegionSearchResultInterface
    {
        /** @var RegionCollection $collection */
        $collection = $this->regionCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var RegionSearchResultInterface $searchResult */
        $searchResult = $this->regionSearchResultFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
