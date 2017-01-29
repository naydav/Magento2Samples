<?php
namespace Engine\CategoryTree\Test\Api\CategoryTreeLoader;

use Engine\Test\AssertArrayEquals;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Engine\Tree\Api\Data\MoveDataInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CategoryTreeMovementTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/category/move';
    const SERVICE_NAME = 'categoryMoveV1';
    const RESOURCE_PATH_GET_TREE = '/V1/category/tree';
    const SERVICE_NAME_GET_TREE = 'categoryTreeV1';
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
    public function testMove()
    {
        $data = [
            'moveData' => [
                MoveDataInterface::ID => 200,
                MoveDataInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                MoveDataInterface::AFTER_ID => 400,
            ],
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Move',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, $data);
        self::assertTrue($response);

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 0,
                        CategoryTreeInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 200,
                    CategoryTreeInterface::TITLE => 'Category-title-1-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 1,
                        CategoryTreeInterface::TITLE => 'Category-title-1-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 2,
                        CategoryTreeInterface::TITLE => 'Category-title-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 300,
                            CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 300,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 200,
                                CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->getTree();
        AssertArrayEquals::assert($expectedData, $tree, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * If afterId is missed then move on first position
     *
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testChangeParentWithoutAfterId()
    {
        $data = [
            'moveData' => [
                MoveDataInterface::ID => 100,
                MoveDataInterface::PARENT_ID => 400,
            ],
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Move',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, $data);
        self::assertTrue($response);

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 100,
                        CategoryTreeInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 100,
                            CategoryTreeInterface::TITLE => 'Category-title-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 100,
                                CategoryInterface::PARENT_ID => 400,
                                CategoryInterface::POSITION => 0,
                                CategoryTreeInterface::TITLE => 'Category-title-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [
                                [
                                    CategoryTreeInterface::ID => 200,
                                    CategoryTreeInterface::TITLE => 'Category-title-1-1',
                                    CategoryTreeInterface::CATEGORY => [
                                        CategoryInterface::CATEGORY_ID => 200,
                                        CategoryInterface::PARENT_ID => 100,
                                        CategoryInterface::POSITION => 100,
                                        CategoryTreeInterface::TITLE => 'Category-title-1-1',
                                    ],
                                    CategoryTreeInterface::CHILDREN => [],
                                ],
                                [
                                    CategoryTreeInterface::ID => 300,
                                    CategoryTreeInterface::TITLE => 'Category-title-1-2',
                                    CategoryTreeInterface::CATEGORY => [
                                        CategoryInterface::CATEGORY_ID => 300,
                                        CategoryInterface::PARENT_ID => 100,
                                        CategoryInterface::POSITION => 200,
                                        CategoryTreeInterface::TITLE => 'Category-title-1-2',
                                    ],
                                    CategoryTreeInterface::CHILDREN => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->getTree();
        AssertArrayEquals::assert($expectedData, $tree, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testChangePositionWithoutChangingParent()
    {
        $data = [
            'moveData' => [
                MoveDataInterface::ID => 300,
                MoveDataInterface::PARENT_ID => 100,
            ],
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Move',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, $data, null, 'all');
        self::assertTrue($response);

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 100,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 200,
                        CategoryInterface::TITLE => 'Category-title-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 300,
                            CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 300,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 0,
                                CategoryInterface::TITLE => 'Category-title-1-2',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                        [
                            CategoryTreeInterface::ID => 200,
                            CategoryTreeInterface::TITLE => 'Category-title-1-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 200,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 1,
                                CategoryInterface::TITLE => 'Category-title-1-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->getTree();
        AssertArrayEquals::assert($expectedData, $tree, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * @return array
     */
    public function getTree()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH_GET_TREE,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME_GET_TREE,
                'operation' => self::SERVICE_NAME . 'GetTree',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);
        return $response;
    }
}
