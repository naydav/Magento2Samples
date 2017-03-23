<?php
namespace Engine\Category\Test\Integration\Model\Category\Source;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RootCategoryCreatingTest extends \PHPUnit_Framework_TestCase
{
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

        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $this->categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
    }

    public function testCreating()
    {
        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->get());

        self::assertEquals('Root', $rootCategory->getTitle());
    }
}
