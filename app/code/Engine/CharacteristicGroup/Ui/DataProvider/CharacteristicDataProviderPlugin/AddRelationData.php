<?php
namespace Engine\CharacteristicGroup\Ui\DataProvider\CharacteristicDataProviderPlugin;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\Characteristic\Ui\DataProvider\CharacteristicDataProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AddRelationData
{
    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @param CharacteristicGroupRepositoryInterface $characteristicGroupRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        CharacteristicGroupRepositoryInterface $characteristicGroupRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->characteristicGroupRepository = $characteristicGroupRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * @param CharacteristicDataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(CharacteristicDataProvider $subject, array $result)
    {
        if ('engine_characteristic_form_data_source' === $subject->getName() && count($result)) {
            $characteristicId = key($result);
            $result[$characteristicId]['characteristic_groups'] = [
                'assigned_characteristic_groups' => $this->getAssignedCharacteristicGroupsData($characteristicId),
            ];
        }
        return $result;
    }

    /**
     * @param int $characteristicId
     * @return array
     */
    private function getAssignedCharacteristicGroupsData($characteristicId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic', $characteristicId)
            ->create();
        $characteristicGroups = $this->characteristicGroupRepository->getList($searchCriteria)->getItems();

        $assignedCharacteristicGroupsData = [];
        foreach ($characteristicGroups as $characteristicGroup) {
            $assignedCharacteristicGroupsData[] = [
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
                => (string)$characteristicGroup->getCharacteristicGroupId(),
                CharacteristicGroupInterface::BACKEND_TITLE => $characteristicGroup->getBackendTitle(),
                CharacteristicGroupInterface::TITLE => $characteristicGroup->getTitle(),
                CharacteristicGroupInterface::IS_ENABLED => (int)$characteristicGroup->getIsEnabled(),
            ];
        }
        return $assignedCharacteristicGroupsData;
    }
}
