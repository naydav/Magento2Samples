<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\Data\CountryInterfaceFactory;
use Engine\Location\Model\Country\ResourceModel\Country as CountryResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @inheritdoc
 */
class GetCountry implements GetCountryInterface
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
     * @param CountryResourceModel $countryResource
     * @param CountryInterfaceFactory $countryFactory
     */
    public function __construct(
        CountryResourceModel $countryResource,
        CountryInterfaceFactory $countryFactory
    ) {
        $this->countryResource = $countryResource;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $countryId): CountryInterface
    {
        /** @var CountryInterface $country */
        $country = $this->countryFactory->create();
        $this->countryResource->load($country, $countryId, CountryInterface::COUNTRY_ID);

        if (null === $country->getCountryId()) {
            throw new NoSuchEntityException(__('Country with id "%id" does not exist.', ['id' => $countryId]));
        }
        return $country;
    }
}
