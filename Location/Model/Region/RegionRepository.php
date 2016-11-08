<?php
namespace Engine\Location\Model\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\Data\RegionInterfaceFactory;
use Engine\Location\Api\Data\RegionSearchResultInterface;
use Engine\Location\Api\Data\RegionSearchResultInterfaceFactory;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\Region\ResourceModel\RegionCollection;
use Engine\Location\Model\Region\ResourceModel\RegionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionRepository implements RegionRepositoryInterface
{
    /**
     * @var RegionInterfaceFactory
     */
    private $regionFactory;

    /**
     * @var RegionCollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var RegionSearchResultInterfaceFactory
     */
    private $regionSearchResultFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param RegionInterfaceFactory $regionFactory
     * @param RegionCollectionFactory $regionCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RegionSearchResultInterfaceFactory $regionSearchResultFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        RegionInterfaceFactory $regionFactory,
        RegionCollectionFactory $regionCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        RegionSearchResultInterfaceFactory $regionSearchResultFactory,
        EntityManager $entityManager
    ) {
        $this->regionFactory = $regionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->regionSearchResultFactory = $regionSearchResultFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($regionId)
    {
        /** @var Region $region */
        $region = $this->regionFactory->create();

        $this->entityManager->load($region, $regionId);
        if (!$region->getRegionId()) {
            throw new NoSuchEntityException(__('Region with id "%1" does not exist.', $regionId));
        }
        return $region;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($regionId)
    {
        $region = $this->get($regionId);
        try {
            $this->entityManager->delete($region);
            return true;
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(RegionInterface $region)
    {
        try {
            $this->entityManager->save($region);
            return $region->getRegionId();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RegionCollection $collection */
        $collection = $this->regionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        foreach ($collection->getItems() as $item) {
            /** @var RegionInterface $item */
            $items[] = $this->get($item->getRegionId());
        }

        /** @var RegionSearchResultInterface $searchResult */
        $searchResult = $this->regionSearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
