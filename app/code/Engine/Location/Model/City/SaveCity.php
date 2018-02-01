<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\ResourceModel\City as CityResourceModel;
use Engine\Location\Model\City\Validator\CityValidatorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class SaveCity implements SaveCityInterface
{
    /**
     * @var CityValidatorInterface
     */
    private $cityValidator;

    /**
     * @var CityResourceModel
     */
    private $cityResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CityValidatorInterface $cityValidator
     * @param CityResourceModel $cityResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        CityValidatorInterface $cityValidator,
        CityResourceModel $cityResource,
        LoggerInterface $logger
    ) {
        $this->cityValidator = $cityValidator;
        $this->cityResource = $cityResource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(CityInterface $city): int
    {
        $validationResult = $this->cityValidator->validate($city);
        if (!$validationResult->isValid()) {
            throw new ValidationException(__('Validation Failed'), null, 0, $validationResult);
        }

        try {
            $this->cityResource->save($city);
            return (int)$city->getCityId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save City'), $e);
        }
    }
}
