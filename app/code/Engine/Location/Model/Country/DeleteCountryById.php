<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountryInterfaceFactory;
use Engine\Location\Model\Country\ResourceModel\Country as CountryResourceModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class DeleteCountryById implements DeleteCountryByIdInterface
{
    /**
     * @var CountryResourceModel
     */
    private $countryResource;

    /**
     * @var CountryInterfaceFactory
     */
    private $countryFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CountryResourceModel $countryResource
     * @param CountryInterfaceFactory $countryFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CountryResourceModel $countryResource,
        CountryInterfaceFactory $countryFactory,
        LoggerInterface $logger
    ) {
        $this->countryResource = $countryResource;
        $this->countryFactory = $countryFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $countryId)
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->create();
        $this->countryResource->load($country, $countryId, CountryInterface::COUNTRY_ID);

        if (null === $country->getCountryId()) {
            throw new NoSuchEntityException(__('Country with id "%id" does not exist.', ['id' => $countryId]));
        }

        try {
            $this->countryResource->delete($country);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete Country'), $e);
        }
    }
}
