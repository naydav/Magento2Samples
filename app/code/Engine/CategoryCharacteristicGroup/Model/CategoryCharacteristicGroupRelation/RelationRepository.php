<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterfaceFactory;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationSearchResultInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationSearchResultInterfaceFactory;;
use Engine\CategoryCharacteristicGroup\Api\RelationRepositoryInterface;
use Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\ResourceModel\RelationCollection;
use Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\ResourceModel\RelationCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RelationRepository implements RelationRepositoryInterface
{
    /**
     * @var RelationInterfaceFactory
     */
    private $relationFactory;

    /**
     * @var RelationValidatorInterface
     */
    private $relationValidator;

    /**
     * @var RelationCollectionFactory
     */
    private $relationCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var RelationSearchResultInterfaceFactory
     */
    private $relationSearchResultFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param RelationInterfaceFactory $relationFactory
     * @param RelationValidatorInterface $relationValidator
     * @param RelationCollectionFactory $relationCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param RelationSearchResultInterfaceFactory $relationSearchResultFactory
     * @param EntityManager $entityManager
     */
    public function __construct(
        RelationInterfaceFactory $relationFactory,
        RelationValidatorInterface $relationValidator,
        RelationCollectionFactory $relationCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        RelationSearchResultInterfaceFactory $relationSearchResultFactory,
        EntityManager $entityManager
    ) {
        $this->relationFactory = $relationFactory;
        $this->relationValidator = $relationValidator;
        $this->relationCollectionFactory = $relationCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->relationSearchResultFactory = $relationSearchResultFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(RelationInterface $relation)
    {
        $this->relationValidator->validate($relation);
        try {
            $this->entityManager->save($relation);
            return $relation->getRelationId();
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($relationId)
    {
        $relation = $this->get($relationId);
        try {
            $this->entityManager->delete($relation);
            return true;
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var RelationCollection $collection */
        $collection = $this->relationCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        $items = [];
        foreach ($collection->getItems() as $item) {
            /** @var RelationInterface $item */
            $items[] = $this->get($item->getRelationId());
        }

        /** @var RelationSearchResultInterface $searchResult */
        $searchResult = $this->relationSearchResultFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * @param int $relationId
     * @return RelationInterface
     * @throws NoSuchEntityException
     */
    private function get($relationId)
    {
        /** @var RelationInterface $relation */
        $relation = $this->relationFactory->create();

        $this->entityManager->load($relation, $relationId);
        if (!$relation->getCategoryId()) {
            throw new NoSuchEntityException(
                __('Relation with id "%1" does not exist.', $relationId)
            );
        }
        return $relation;
    }
}
