<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CitySearchResultInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param CityRepositoryInterface $cityRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        CityRepositoryInterface $cityRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->cityRepository = $cityRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @param int $regionId
     * @return CitySearchResultInterface
     */
    public function getList($regionId)
    {
        $this->searchCriteriaBuilder->addFilter(CityInterface::REGION_ID, (int)$regionId);
        $this->sortOrderBuilder->setField(CityInterface::POSITION)
            ->setAscendingDirection();
        $sortOrder = $this->sortOrderBuilder->create();
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $result = $this->cityRepository->getList($searchCriteria);
        return $result;
    }
}
