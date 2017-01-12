<?php
namespace Engine\Category\Model\Category\Source;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\ResourceModel\CategoryCollection;
use Engine\Category\Model\Category\ResourceModel\CategoryCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class GroupedCategory implements OptionSourceInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var array|null
     */
    private $data;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RootCategoryIdProviderInterface $rootCategoryIdProvider
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RootCategoryIdProviderInterface $rootCategoryIdProvider
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->rootCategoryIdProvider = $rootCategoryIdProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (null === $this->data) {
            $this->data = [];
            /** @var CategoryCollection $categoryCollection */
            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection
                ->addStoreData()
                ->setOrder(CategoryInterface::POSITION, CategoryCollection::SORT_ORDER_ASC);

            if ($categoryCollection->getSize()) {
                $categoryById = [];
                foreach ($categoryCollection as $category) {
                    /** @var CategoryInterface $category */
                    $categoryById[$category->getCategoryId()]['value'] = $category->getCategoryId();
                    $categoryById[$category->getCategoryId()]['label'] = sprintf(
                        '%s (ID: %d)',
                        $category->getTitle(),
                        $category->getCategoryId()
                    );
                    $categoryById[$category->getCategoryId()]['is_active'] = $category->getIsEnabled();
                    if ($category->getCategoryId() !== null) {
                        $categoryById[$category->getParentId()]['optgroup'][] =
                            &$categoryById[$category->getCategoryId()];
                    }
                }
                $this->data = [
                    $categoryById[$this->rootCategoryIdProvider->provide()]
                ];
            }
        }
        return $this->data;
    }
}
