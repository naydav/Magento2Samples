<?php
namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\HydratorInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class DataRegionHelper
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        HydratorInterface $hydrator
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param RegionInterface $region
     * @param array $data
     * @return RegionInterface
     */
    public function populateWithArray(RegionInterface $region, array $data)
    {
        $this->dataObjectHelper->populateWithArray($region, $data, RegionInterface::class);
        return $region;
    }

    /**
     * @param RegionInterface $region
     * @return array
     */
    public function hydrate(RegionInterface $region)
    {
        $data = $this->hydrator->extract($region);
        return $data;
    }
}
