<?php
declare(strict_types=1);

namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Model\City\ResourceModel\City as CityResourceModel;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class DeleteCityById implements DeleteCityByIdInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CityResourceModel $cityResource
     * @param CityInterfaceFactory $cityFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CityResourceModel $cityResource,
        CityInterfaceFactory $cityFactory,
        LoggerInterface $logger
    ) {
        $this->cityResource = $cityResource;
        $this->cityFactory = $cityFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $cityId)
    {
        /** @var CityInterface $city */
        $city = $this->cityFactory->create();
        $this->cityResource->load($city, $cityId, CityInterface::CITY_ID);

        if (null === $city->getCityId()) {
            throw new NoSuchEntityException(__('City with id "%id" does not exist.', ['id' => $cityId]));
        }

        try {
            $this->cityResource->delete($city);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete City'), $e);
        }
    }
}
