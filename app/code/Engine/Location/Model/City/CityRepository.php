<?php
namespace Engine\Location\Model\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\CityInterfaceFactory;
use Engine\Location\Api\Data\CitySearchResultInterface;
use Engine\Location\Api\Data\CitySearchResultInterfaceFactory;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Model\City\ResourceModel\CityCollection;
use Engine\Location\Model\City\ResourceModel\CityCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CityRepository implements CityRepositoryInterface
{
    /**
     * @var CityInterfaceFactory
     */
    private $cityFactory;

    /**
     * @var CityValidatorInterface
     */
    private $cityValidator;

    /**
     * @var CityCollectionFactory
     */
    private $cityCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CitySearchResultInterfaceFactory
     */
    private $citySearchResultFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param CityInterfaceFactory $cityFactory
     * @param CityValidatorInterface $cityValidator
     * @param CityCollectionFactory $cityCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CitySearchResultInterfaceFactory $citySearchResultFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        CityInterfaceFactory $cityFactory,
        CityValidatorInterface $cityValidator,
        CityCollectionFactory $cityCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CitySearchResultInterfaceFactory $citySearchResultFactory,
        EntityManager $entityManager
    ) {
        $this->cityFactory = $cityFactory;
        $this->cityValidator = $cityValidator;
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->citySearchResultFactory = $citySearchResultFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cityId)
    {
        /** @var CityInterface $city */
        $city = $this->cityFactory->create();

        $this->entityManager->load($city, $cityId);
        if (!$city->getCityId()) {
            throw new NoSuchEntityException(
                __('City with id "%1" does not exist.', $cityId)
            );
        }
        return $city;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cityId)
    {
        $city = $this->get($cityId);
        try {
            $this->entityManager->delete($city);
            return true;
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CityInterface $city)
    {
        $this->cityValidator->validate($city);
        try {
            $this->entityManager->save($city);
            return $city->getCityId();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CityCollection $collection */
        $collection = $this->cityCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        foreach ($collection->getItems() as $item) {
            /** @var CityInterface $item */
            $items[] = $this->get($item->getCityId());
        }

        /** @var CitySearchResultInterface $searchResult */
        $searchResult = $this->citySearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
