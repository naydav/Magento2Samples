<?php
namespace Engine\Category\Test\Integration\Model\Category\Source;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\Source\Category;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Category
     */
    private $categorySource;

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
        $this->categorySource = Bootstrap::getObjectManager()->create(Category::class);
        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list_global_scope.php
     */
    public function testToOptionArray()
    {
        $options = $this->categorySource->toOptionArray();
        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->provide());

        $expectedData = [
            [
                'value' => $rootCategory->getCategoryId(),
                'label' => sprintf('%s (ID: %d)', $rootCategory->getTitle(), $rootCategory->getCategoryId()),
            ],
            [
                'value' => 400,
                'label' => 'Category-title-1 (ID: 400)',
            ],
            [
                'value' => 200,
                'label' => 'Category-title-2 (ID: 200)',
            ],
            [
                'value' => 300,
                'label' => 'Category-title-2 (ID: 300)',
            ],
            [
                'value' => 100,
                'label' => 'Category-title-3 (ID: 100)',
            ],
        ];
        self::assertEquals($expectedData, $options);
    }

    public function testToOptionArrayIfCategoriesAreNotExist()
    {
        $options = $this->categorySource->toOptionArray();
        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->provide());
        $expectedData = [
            [
                'value' => $rootCategory->getCategoryId(),
                'label' => sprintf('%s (ID: %d)', $rootCategory->getTitle(), $rootCategory->getCategoryId()),
            ],
        ];
        self::assertEquals($expectedData, $options);
    }
}
