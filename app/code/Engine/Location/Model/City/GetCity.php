<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Model\City\ResourceModel\City as CityResourceModel;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @inheritdoc
 */
class GetCity implements GetCityInterface
{
    /**
     * @var CityResourceModel
     */
    private $cityResource;

    /**
     * @var CityInterfaceFactory
     */
    private $cityFactory;

    /**
     * @param CityResourceModel $cityResource
     * @param CityInterfaceFactory $cityFactory
     */
    public function __construct(
        CityResourceModel $cityResource,
        CityInterfaceFactory $cityFactory
    ) {
        $this->cityResource = $cityResource;
        $this->cityFactory = $cityFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $cityId): CityInterface
    {
        /** @var CityInterface $city */
        $city = $this->cityFactory->create();
        $this->cityResource->load($city, $cityId, CityInterface::CITY_ID);

        if (null === $city->getCityId()) {
            throw new NoSuchEntityException(__('City with id "%id" does not exist.', ['id' => $cityId]));
        }
        return $city;
    }
}
