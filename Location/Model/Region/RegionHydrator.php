<?php
namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * This class has been created only as extension point for plugins
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionHydrator
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(DataObjectHelper $dataObjectHelper)
    {
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @param RegionInterface $region
     * @param array $data
     * @return RegionInterface
     */
    public function hydrate(RegionInterface $region, array $data)
    {
        $this->dataObjectHelper->populateWithArray($region, $data, RegionInterface::class);
        return $region;
    }
}
