<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterfaceFactory;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupSearchResultInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupSearchResultInterfaceFactory;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel\CharacteristicGroupCollection;
use Engine\CharacteristicGroup\Model\CharacteristicGroup\ResourceModel\CharacteristicGroupCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroupRepository implements CharacteristicGroupRepositoryInterface
{
    /**
     * @var CharacteristicGroupInterfaceFactory
     */
    private $characteristicGroupFactory;

    /**
     * @var CharacteristicGroupValidatorInterface
     */
    private $characteristicGroupValidator;

    /**
     * @var CharacteristicGroupCollectionFactory
     */
    private $characteristicGroupCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CharacteristicGroupSearchResultInterfaceFactory
     */
    private $characteristicGroupSearchResultFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param CharacteristicGroupInterfaceFactory $characteristicGroupFactory
     * @param CharacteristicGroupValidatorInterface $characteristicGroupValidator
     * @param CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CharacteristicGroupSearchResultInterfaceFactory $characteristicGroupSearchResultFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        CharacteristicGroupInterfaceFactory $characteristicGroupFactory,
        CharacteristicGroupValidatorInterface $characteristicGroupValidator,
        CharacteristicGroupCollectionFactory $characteristicGroupCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CharacteristicGroupSearchResultInterfaceFactory $characteristicGroupSearchResultFactory,
        EntityManager $entityManager
    ) {
        $this->characteristicGroupFactory = $characteristicGroupFactory;
        $this->characteristicGroupValidator = $characteristicGroupValidator;
        $this->characteristicGroupCollectionFactory = $characteristicGroupCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->characteristicGroupSearchResultFactory = $characteristicGroupSearchResultFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function get($characteristicGroupId)
    {
        /** @var CharacteristicGroupInterface $characteristicGroup */
        $characteristicGroup = $this->characteristicGroupFactory->create();

        $this->entityManager->load($characteristicGroup, $characteristicGroupId);
        if (!$characteristicGroup->getCharacteristicGroupId()) {
            throw new NoSuchEntityException(
                __('Characteristic Group with id "%1" does not exist.', $characteristicGroupId)
            );
        }
        return $characteristicGroup;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($characteristicGroupId)
    {
        $characteristicGroup = $this->get($characteristicGroupId);
        try {
            $this->entityManager->delete($characteristicGroup);
            return true;
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CharacteristicGroupInterface $characteristicGroup)
    {
        $this->characteristicGroupValidator->validate($characteristicGroup);
        try {
            $this->entityManager->save($characteristicGroup);
            return $characteristicGroup->getCharacteristicGroupId();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CharacteristicGroupCollection $collection */
        $collection = $this->characteristicGroupCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        foreach ($collection->getItems() as $item) {
            /** @var CharacteristicGroupInterface $item */
            $items[] = $this->get($item->getCharacteristicGroupId());
        }

        /** @var CharacteristicGroupSearchResultInterface $searchResult */
        $searchResult = $this->characteristicGroupSearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }
}
