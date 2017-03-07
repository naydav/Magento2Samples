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
class CharacteristicGroupRelationsProcessor
{
    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

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
     * @param CharacteristicRepositoryInterface $characteristicRepository
     * @param RelationInterfaceFactory $relationFactory
     * @param RelationRepositoryInterface $relationRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        CharacteristicRepositoryInterface $characteristicRepository,
        RelationInterfaceFactory $relationFactory,
        RelationRepositoryInterface $relationRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        HydratorInterface $hydrator
    ) {
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->characteristicRepository = $characteristicRepository;
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

            $characteristic = $this->characteristicRepository->get(
                $relationData[RelationInterface::CHARACTERISTIC_ID]
            );
            if (isset($currentRelationMap[$characteristic->getCharacteristicId()])) {
                $relationData[RelationInterface::RELATION_ID] =
                    $currentRelationMap[$characteristic->getCharacteristicId()]->getRelationId();
                unset($currentRelationMap[$characteristic->getCharacteristicId()]);
            }

            $relationData[RelationInterface::CHARACTERISTIC_GROUP_ID]
                = $characteristicGroup->getCharacteristicGroupId();
            $this->saveRelation($relationData);
        }
        $this->deleteRelations($currentRelationMap);
    }

    /**
     * Key is characteristic id, value is relation
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
                $relationMap[$relation->getCharacteristicId()] = $relation;
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
        if (!isset($relationData[RelationInterface::CHARACTERISTIC_ID])) {
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
