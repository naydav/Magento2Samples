<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountrySearchResultInterface;
use Engine\Location\Api\Data\CountrySearchResultInterfaceFactory;
use Engine\Location\Model\Country\ResourceModel\CountryCollection;
use Engine\Location\Model\Country\ResourceModel\CountryCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class GetCountryList implements GetCountryListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CountryCollectionFactory
     */
    private $countryCollectionFactory;

    /**
     * @var CountrySearchResultInterfaceFactory
     */
    private $countrySearchResultFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param CountrySearchResultInterfaceFactory $countrySearchResultFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CollectionProcessorInterface $collectionProcessor,
        CountryCollectionFactory $countryCollectionFactory,
        CountrySearchResultInterfaceFactory $countrySearchResultFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionProcessor = $collectionProcessor;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->countrySearchResultFactory = $countrySearchResultFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria = null): CountrySearchResultInterface
    {
        /** @var CountryCollection $collection */
        $collection = $this->countryCollectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var CountrySearchResultInterface $searchResult */
        $searchResult = $this->countrySearchResultFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }
}
