<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionSearchResultInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @inheritdoc
 */
class RegionRepository implements RegionRepositoryInterface
{
    /**
     * @var SaveRegionInterface
     */
    private $saveRegion;

    /**
     * @var GetRegionInterface
     */
    private $getRegion;

    /**
     * @var DeleteRegionByIdInterface
     */
    private $deleteRegionById;

    /**
     * @var GetRegionListInterface
     */
    private $getRegionList;

    /**
     * @param SaveRegionInterface $saveRegion
     * @param GetRegionInterface $getRegion
     * @param DeleteRegionByIdInterface $deleteRegionById
     * @param GetRegionListInterface $getRegionList
     */
    public function __construct(
        SaveRegionInterface $saveRegion,
        GetRegionInterface $getRegion,
        DeleteRegionByIdInterface $deleteRegionById,
        GetRegionListInterface $getRegionList
    ) {
        $this->saveRegion = $saveRegion;
        $this->getRegion = $getRegion;
        $this->deleteRegionById = $deleteRegionById;
        $this->getRegionList = $getRegionList;
    }

    /**
     * @inheritdoc
     */
    public function save(RegionInterface $region): int
    {
        return $this->saveRegion->execute($region);
    }

    /**
     * @inheritdoc
     */
    public function get(int $regionId): RegionInterface
    {
        return $this->getRegion->execute($regionId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $regionId)
    {
        $this->deleteRegionById->execute($regionId);
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): RegionSearchResultInterface
    {
        return $this->getRegionList->execute($searchCriteria);
    }
}
