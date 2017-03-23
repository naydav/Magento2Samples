<?php
namespace Engine\Category\Test\Integration\Model\Category\Source;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\Source\GroupedCategorySource;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class GroupedCategorySourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GroupedCategorySource
     */
    private $groupedCategorySource;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    protected function setUp()
    {
        parent::setUp();

        // use create instead get for prevent internal object caching
        $this->groupedCategorySource = Bootstrap::getObjectManager()->create(GroupedCategorySource::class);
        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testToOptionArray()
    {
        $options = $this->groupedCategorySource->toOptionArray();
        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->get());

        $expectedData = [
            [
                'value' => $rootCategory->getCategoryId(),
                'label' => sprintf('%s (ID: %d)', $rootCategory->getTitle(), $rootCategory->getCategoryId()),
                'is_active' => 1,
                'optgroup' => [
                    [
                        'value' => 400,
                        'label' => 'Category-title-2 (ID: 400)',
                        'is_active' => 0,
                    ],
                    [
                        'value' => 100,
                        'label' => 'Category-title-1 (ID: 100)',
                        'is_active' => 1,
                        'optgroup' => [
                            [
                                'value' => 200,
                                'label' => 'Category-title-1-1 (ID: 200)',
                                'is_active' => 1,
                            ],
                            [
                                'value' => 300,
                                'label' => 'Category-title-1-2 (ID: 300)',
                                'is_active' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        self::assertEquals($expectedData, $options);
    }

    public function testToOptionArrayIfCategoriesAreNotExist()
    {
        $options = $this->groupedCategorySource->toOptionArray();
        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->get());
        $expectedData = [
            [
                'value' => $rootCategory->getCategoryId(),
                'label' => sprintf('%s (ID: %d)', $rootCategory->getTitle(), $rootCategory->getCategoryId()),
                'is_active' => 1,
            ],
        ];
        self::assertEquals($expectedData, $options);
    }
}
