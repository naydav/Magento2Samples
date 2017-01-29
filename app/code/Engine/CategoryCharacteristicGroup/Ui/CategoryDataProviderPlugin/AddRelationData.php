<?php
namespace Engine\CategoryCharacteristicGroup\Ui\CategoryDataProviderPlugin;

use Engine\Category\Ui\DataProvider\CategoryDataProvider;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
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
     * @param CategoryDataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(CategoryDataProvider $subject, array $result)
    {
        if ('engine_category_form_data_source' === $subject->getName() && count($result)) {
            $categoryId = key($result);
            $result[$categoryId]['characteristic_groups'] = [
                'assigned_characteristic_groups' => $this->getAssignedCharacteristicGroupsData($categoryId),
            ];
        }
        return $result;
    }

    /**
     * @param int $categoryId
     * @return array
     */
    private function getAssignedCharacteristicGroupsData($categoryId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_category', $categoryId)
            ->create();
        $characteristicGroups = $this->characteristicGroupRepository->getList($searchCriteria)->getItems();

        $assignedCharacteristicGroupsData = [];
        foreach ($characteristicGroups as $characteristicGroup) {
            $assignedCharacteristicGroupsData[] = [
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
                => (string)$characteristicGroup->getCharacteristicGroupId(),
                CharacteristicGroupInterface::TITLE => $characteristicGroup->getTitle(),
                CharacteristicGroupInterface::BACKEND_TITLE => $characteristicGroup->getBackendTitle(),
                CharacteristicGroupInterface::IS_ENABLED => (int)$characteristicGroup->getIsEnabled(),
            ];
        }
        return $assignedCharacteristicGroupsData;
    }
}
