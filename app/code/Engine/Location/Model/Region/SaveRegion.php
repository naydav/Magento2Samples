<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\Region as RegionResourceModel;
use Engine\Location\Model\Region\Validator\RegionValidatorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class SaveRegion implements SaveRegionInterface
{
    /**
     * @var RegionValidatorInterface
     */
    private $regionValidator;

    /**
     * @var RegionResourceModel
     */
    private $regionResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RegionValidatorInterface $regionValidator
     * @param RegionResourceModel $regionResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        RegionValidatorInterface $regionValidator,
        RegionResourceModel $regionResource,
        LoggerInterface $logger
    ) {
        $this->regionValidator = $regionValidator;
        $this->regionResource = $regionResource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(RegionInterface $region): int
    {
        $validationResult = $this->regionValidator->validate($region);
        if (!$validationResult->isValid()) {
            throw new ValidationException(__('Validation Failed'), null, 0, $validationResult);
        }

        try {
            $this->regionResource->save($region);
            return (int)$region->getRegionId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Region'), $e);
        }
    }
}
