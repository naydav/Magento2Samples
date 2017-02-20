<?php
namespace Engine\CategoryCharacteristicGroup\Model;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterfaceFactory;
use Engine\CategoryCharacteristicGroup\Api\RelationRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicGroupRelationsProcessor
{
    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var RelationInterfaceFactory
     */
    private $relationFactory;

    /**
     * @var RelationRepositoryInterface
     */
    private $relationRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param CategoryRepositoryInterface $categoryRepository
     * @param RelationInterfaceFactory $relationFactory
     * @param RelationRepositoryInterface $relationRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        CategoryRepositoryInterface $categoryRepository,
        RelationInterfaceFactory $relationFactory,
        RelationRepositoryInterface $relationRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        HydratorInterface $hydrator
    ) {
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->categoryRepository = $categoryRepository;
        $this->relationFactory = $relationFactory;
        $this->relationRepository = $relationRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $characteristicGroupId
     * @param array $relationsData
     * @return void
     * @throws LocalizedException
     */
    public function process($characteristicGroupId, array $relationsData)
    {
        $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);
        $currentRelationMap = $this->getCurrentRelationMap($characteristicGroup->getCharacteristicGroupId());

        foreach ($relationsData as $relationData) {
            $this->validateRelationData($relationData);

            $category = $this->categoryRepository->get($relationData[RelationInterface::CATEGORY_ID]);
            if (isset($currentRelationMap[$category->getCategoryId()])) {
                $relationData[RelationInterface::RELATION_ID] =
                    $currentRelationMap[$category->getCategoryId()]->getRelationId();
                unset($currentRelationMap[$category->getCategoryId()]);
            }

            $relationData[RelationInterface::CHARACTERISTIC_GROUP_ID] =
                $characteristicGroup->getCharacteristicGroupId();
            $this->saveRelation($relationData);
        }
        $this->deleteRelations($currentRelationMap);
    }

    /**
     * Key is category id, value is relation
     *
     * @param int $characteristicGroupId
     * @return RelationInterface[]
     */
    private function getCurrentRelationMap($characteristicGroupId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(RelationInterface::CHARACTERISTIC_GROUP_ID, $characteristicGroupId)
            ->create();
        $relations = $this->relationRepository->getList($searchCriteria)->getItems();

        $relationMap = [];
        if ($relations) {
            foreach ($relations as $relation) {
                $relationMap[$relation->getCategoryId()] = $relation;
            }
        }
        return $relationMap;
    }

    /**
     * @param array $relationData
     * @return void
     * @throws LocalizedException
     */
    private function validateRelationData(array $relationData)
    {
        if (!isset($relationData[RelationInterface::CATEGORY_ID])) {
            throw new LocalizedException(__('Wrong Category to Characteristic Group relation parameters.'));
        }
    }

    /**
     * @param array $relationData
     * @return void
     */
    private function saveRelation(array $relationData)
    {
        /** @var RelationInterface $relation */
        $relation = $this->relationFactory->create();
        $this->hydrator->hydrate($relation, $relationData);
        $this->relationRepository->save($relation);
    }

    /**
     * @param array $relations
     * @return void
     */
    private function deleteRelations(array $relations)
    {
        foreach ($relations as $relation) {
            $this->relationRepository->deleteById($relation->getRelationId());
        }
    }
}
