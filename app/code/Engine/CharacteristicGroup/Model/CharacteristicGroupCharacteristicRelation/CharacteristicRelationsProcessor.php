<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation;

use Engine\Characteristic\Api\CharacteristicRepositoryInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterfaceFactory;
use Engine\CharacteristicGroup\Api\RelationRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CharacteristicRelationsProcessor
{
    /**
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

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
     * @param CharacteristicRepositoryInterface $characteristicRepository
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param RelationInterfaceFactory $relationFactory
     * @param RelationRepositoryInterface $relationRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        CharacteristicRepositoryInterface $characteristicRepository,
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        RelationInterfaceFactory $relationFactory,
        RelationRepositoryInterface $relationRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        HydratorInterface $hydrator
    ) {
        $this->characteristicRepository = $characteristicRepository;
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->relationFactory = $relationFactory;
        $this->relationRepository = $relationRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->hydrator = $hydrator;
    }

    /**
     * @param int $characteristicId
     * @param array $relationsData
     * @return void
     * @throws LocalizedException
     */
    public function process($characteristicId, array $relationsData)
    {
        $characteristic = $this->characteristicRepository->get($characteristicId);
        $currentRelationMap = $this->getCurrentRelationMap($characteristic->getCharacteristicId());

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

            $relationData[RelationInterface::CHARACTERISTIC_ID] = $characteristic->getCharacteristicId();
            $this->saveRelation($relationData);
        }
        $this->deleteRelations($currentRelationMap);
    }

    /**
     * Key is characteristic group id, value is relation
     *
     * @param int $characteristicId
     * @return RelationInterface[]
     */
    private function getCurrentRelationMap($characteristicId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(RelationInterface::CHARACTERISTIC_ID, $characteristicId)
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
            throw new LocalizedException(__('Wrong Characteristic Group to Characteristic relation parameters.'));
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
