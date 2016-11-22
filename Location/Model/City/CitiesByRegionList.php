<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CitySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CitiesByRegionList
{
    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param CityRepositoryInterface $cityRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CityRepositoryInterface $cityRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->cityRepository = $cityRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @param int $regionId
     * @return CitySearchResultInterface
     */
    public function getList($regionId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CityInterface::REGION_ID, (int)$regionId);
        $this->sortOrderBuilder->setField(CityInterface::POSITION)
            ->setAscendingDirection();
        $sortOrder = $this->sortOrderBuilder->create();
        $this->searchCriteriaBuilderFactory->addSortOrder($sortOrder);
        $searchCriteria = $this->searchCriteriaBuilderFactory->create();
        $result = $this->cityRepository->getList($searchCriteria);
        return $result;
    }
}
