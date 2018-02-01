<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CitySearchResultInterface;
use Engine\Location\Api\Data\CitySearchResultInterfaceFactory;
use Engine\Location\Model\City\ResourceModel\CityCollection;
use Engine\Location\Model\City\ResourceModel\CityCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class GetCityList implements GetCityListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CityCollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var CitySearchResultInterfaceFactory
     */
    private $citySearchResultFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CityCollectionFactory $cityCollectionFactory
     * @param CitySearchResultInterfaceFactory $citySearchResultFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        CityCollectionFactory $cityCollectionFactory,
        CitySearchResultInterfaceFactory $citySearchResultFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->citySearchResultFactory = $citySearchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria = null): CitySearchResultInterface
    {
        /** @var CityCollection $collection */
        $collection = $this->cityCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var CitySearchResultInterface $searchResult */
        $searchResult = $this->citySearchResultFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
