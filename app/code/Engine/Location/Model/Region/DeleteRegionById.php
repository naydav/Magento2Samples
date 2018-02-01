<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Model\Region\ResourceModel\Region as RegionResourceModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class DeleteRegionById implements DeleteRegionByIdInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RegionResourceModel $regionResource
     * @param RegionInterfaceFactory $regionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        RegionResourceModel $regionResource,
        RegionInterfaceFactory $regionFactory,
        LoggerInterface $logger
    ) {
        $this->regionResource = $regionResource;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $regionId)
    {
        /** @var RegionInterface $region */
        $region = $this->regionFactory->create();
        $this->regionResource->load($region, $regionId, RegionInterface::REGION_ID);

        if (null === $region->getRegionId()) {
            throw new NoSuchEntityException(__('Region with id "%id" does not exist.', ['id' => $regionId]));
        }

        try {
            $this->regionResource->delete($region);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete Region'), $e);
        }
    }
}
