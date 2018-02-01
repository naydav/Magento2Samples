<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Model\Region\ResourceModel\Region as RegionResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @inheritdoc
 */
class GetRegion implements GetRegionInterface
{
    /**
     * @var RegionResourceModel
     */
    private $regionResource;

    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @param RegionResourceModel $regionResource
     * @param RegionInterfaceFactory $regionFactory
     */
    public function __construct(
        RegionResourceModel $regionResource,
        RegionInterfaceFactory $regionFactory
    ) {
        $this->regionResource = $regionResource;
        $this->regionFactory = $regionFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $regionId): RegionInterface
    {
        /** @var RegionInterface $region */
        $region = $this->regionFactory->create();
        $this->regionResource->load($region, $regionId, RegionInterface::REGION_ID);

        if (null === $region->getRegionId()) {
            throw new NoSuchEntityException(__('Region with id "%id" does not exist.', ['id' => $regionId]));
        }
        return $region;
    }
}
