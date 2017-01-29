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
class CategoryRelationsProcessor
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

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
     * @param CategoryRepositoryInterface $categoryRepository
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param RelationInterfaceFactory $relationFactory
     * @param RelationRepositoryInterface $relationRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        RelationInterfaceFactory $relationFactory,
        RelationRepositoryInterface $relationRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        HydratorInterface $hydrator
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->relationFactory = $relationFactory;
        $this->relationRepository = $relationRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $categoryId
     * @param array $relationsData
     * @return void
     * @throws LocalizedException
     */
    public function processRelations($categoryId, array $relationsData)
    {
        $category = $this->categoryRepository->get($categoryId);
        $currentRelationMap = $this->getCurrentRelationMap($category->getCategoryId());

        foreach ($relationsData as $relationData) {
            $this->validateRelationData($relationData);

            $characteristicGroup = $this->characteristicGroupRepository->get(
                $relationData[RelationInterface::CHARACTERISTIC_GROUP_ID]
            );
            if (isset($currentRelationMap[$characteristicGroup->getCharacteristicGroupId()])) {
                $relationData[RelationInterface::RELATION_ID] =
                    $currentRelationMap[$characteristicGroup->getCharacteristicGroupId()]->getRelationId();
                unset($currentRelationMap[$characteristicGroup->getCharacteristicGroupId()]);
            }

            $relationData[RelationInterface::CATEGORY_ID] = $category->getCategoryId();
            $this->saveRelation($relationData);
        }
        $this->deleteRelations($currentRelationMap);
    }

    /**
     * Key is characteristic group id, value is relation
     *
     * @param int $categoryId
     * @return RelationInterface[]
     */
    private function getCurrentRelationMap($categoryId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(RelationInterface::CATEGORY_ID, $categoryId)
            ->create();
        $relations = $this->relationRepository->getList($searchCriteria)->getItems();

        $relationMap = [];
        if ($relations) {
            foreach ($relations as $relation) {
                $relationMap[$relation->getCharacteristicGroupId()] = $relation;
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
        if (!isset($relationData[RelationInterface::CHARACTERISTIC_GROUP_ID])) {
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
