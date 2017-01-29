<?php
namespace Engine\CategoryTree\Test\Api\CategoryTreeLoader;

use Engine\Test\AssertArrayEquals;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class LoadCategoryTreeTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/category/tree';
    const SERVICE_NAME = 'categoryTreeV1';
    /**#@-*/

    /**
     * @var CategoryInterface
     */
    private $rootCategory;

    protected function setUp()
    {
        parent::setUp();

        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = Bootstrap::getObjectManager()->get(CategoryRepositoryInterface::class);
        $this->rootCategory = $categoryRepository->get($rootCategoryIdProvider->provide());
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testGetTree()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetTree',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::URL_KEY =>  $this->rootCategory->getUrlKey(),
                CategoryInterface::IS_ANCHOR =>  $this->rootCategory->getIsAnchor(),
                CategoryInterface::IS_ENABLED => $this->rootCategory->getIsEnabled(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
                CategoryInterface::DESCRIPTION => $this->rootCategory->getDescription(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::URL_KEY => 'Category-urlKey-400',
                        CategoryInterface::IS_ANCHOR => false,
                        CategoryInterface::IS_ENABLED => false,
                        CategoryInterface::POSITION => 100,
                        CategoryInterface::TITLE => 'Category-title-2',
                        CategoryInterface::DESCRIPTION => 'Category-description-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::URL_KEY => 'Category-urlKey-100',
                        CategoryInterface::IS_ANCHOR => true,
                        CategoryInterface::IS_ENABLED => true,
                        CategoryInterface::POSITION => 200,
                        CategoryInterface::TITLE => 'Category-title-1',
                        CategoryInterface::DESCRIPTION => 'Category-description-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 200,
                            CategoryTreeInterface::TITLE => 'Category-title-1-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 200,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::URL_KEY => 'Category-urlKey-200',
                                CategoryInterface::IS_ANCHOR => true,
                                CategoryInterface::IS_ENABLED => true,
                                CategoryInterface::POSITION => 100,
                                CategoryInterface::TITLE => 'Category-title-1-1',
                                CategoryInterface::DESCRIPTION => 'Category-description-1-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                        [
                            CategoryTreeInterface::ID => 300,
                            CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 300,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::URL_KEY => 'Category-urlKey-300',
                                CategoryInterface::IS_ANCHOR => false,
                                CategoryInterface::IS_ENABLED => false,
                                CategoryInterface::POSITION => 200,
                                CategoryInterface::TITLE => 'Category-title-1-2',
                                CategoryInterface::DESCRIPTION => 'Category-description-1-2',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        AssertArrayEquals::assert($expectedData, $response, [ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testGetTreeWithSearchCriteria()
    {
        $searchCriteria = [
            'filter_groups' => [
                [
                    'filters' => [
                        [
                            'field' => CategoryInterface::IS_ENABLED,
                            'value' => 1,
                            'condition_type' => 'eq',
                        ],
                    ],
                ],
            ],
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?'
                    . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetTree',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::URL_KEY =>  $this->rootCategory->getUrlKey(),
                CategoryInterface::IS_ANCHOR =>  $this->rootCategory->getIsAnchor(),
                CategoryInterface::IS_ENABLED => $this->rootCategory->getIsEnabled(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
                CategoryInterface::DESCRIPTION => $this->rootCategory->getDescription(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::URL_KEY => 'Category-urlKey-100',
                        CategoryInterface::IS_ANCHOR => true,
                        CategoryInterface::IS_ENABLED => true,
                        CategoryInterface::POSITION => 200,
                        CategoryInterface::TITLE => 'Category-title-1',
                        CategoryInterface::DESCRIPTION => 'Category-description-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 200,
                            CategoryTreeInterface::TITLE => 'Category-title-1-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 200,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::URL_KEY => 'Category-urlKey-200',
                                CategoryInterface::IS_ANCHOR => true,
                                CategoryInterface::IS_ENABLED => true,
                                CategoryInterface::POSITION => 100,
                                CategoryInterface::TITLE => 'Category-title-1-1',
                                CategoryInterface::DESCRIPTION => 'Category-description-1-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        AssertArrayEquals::assert($expectedData, $response, [ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
    }
}
