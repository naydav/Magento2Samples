<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Model\Country\ResourceModel\Country as CountryResourceModel;
use Engine\Location\Model\Country\Validator\CountryValidatorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Validation\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class SaveCountry implements SaveCountryInterface
{
    /**
     * @var CountryValidatorInterface
     */
    private $countryValidator;

    /**
     * @var CountryResourceModel
     */
    private $countryResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CountryValidatorInterface $countryValidator
     * @param CountryResourceModel $countryResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        CountryValidatorInterface $countryValidator,
        CountryResourceModel $countryResource,
        LoggerInterface $logger
    ) {
        $this->countryValidator = $countryValidator;
        $this->countryResource = $countryResource;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(CountryInterface $country): int
    {
        $validationResult = $this->countryValidator->validate($country);
        if (!$validationResult->isValid()) {
            throw new ValidationException(__('Validation Failed'), null, 0, $validationResult);
        }

        try {
            $this->countryResource->save($country);
            return (int)$country->getCountryId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Country'), $e);
        }
    }
}
