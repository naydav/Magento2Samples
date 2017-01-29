<?php
namespace Engine\CategoryCharacteristicGroup\Ui\CharacteristicGroupDataProviderPlugin;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\Source\CategorySource;
use Engine\CharacteristicGroup\Ui\DataProvider\CharacteristicGroupDataProvider;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AddRelationData
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var CategorySource
     */
    private $categorySource;

    /**
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param CategorySource $categorySource
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        CategorySource $categorySource
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->categorySource = $categorySource;
    }

    /**
     * @param CharacteristicGroupDataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(CharacteristicGroupDataProvider $subject, array $result)
    {
        if ('engine_characteristic_group_form_data_source' === $subject->getName() && count($result)) {
            $characteristicGroupId = key($result);
            $result[$characteristicGroupId]['categories'] = [
                'assigned_categories' => $this->getAssignedCategoriesData($characteristicGroupId),
            ];
        }
        return $result;
    }

    /**
     * @param int $characteristicGroupId
     * @return array
     */
    private function getAssignedCategoriesData($characteristicGroupId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic_group', $characteristicGroupId)
            ->create();
        $categories = $this->categoryRepository->getList($searchCriteria)->getItems();
        $categoryMap = array_column($this->categorySource->toOptionArray(), 'label', 'value');

        $assignedCategoriesData = [];
        foreach ($categories as $category) {
            $assignedCategoriesData[] = [
                CategoryInterface::CATEGORY_ID => (string)$category->getCategoryId(),
                CategoryInterface::TITLE => $category->getTitle(),
                CategoryInterface::PARENT_ID => null !== $category->getParentId()
                    ? $categoryMap[$category->getParentId()] : null,
                CategoryInterface::IS_ENABLED => (int)$category->getIsEnabled(),
            ];
        }
        return $assignedCategoriesData;
    }
}
