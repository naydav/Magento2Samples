<?php
namespace Engine\CategoryTree\Test\Api\CategoryTreeLoader;

use Engine\Framework\Test\AssertArrayEquals;
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
class LoadCategorySubTreeTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/category/tree';
    const SERVICE_NAME = 'categoryTreeV1';
    /**#@-*/

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    protected function setUp()
    {
        parent::setUp();

        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testGetSubTree()
    {
        $data = ['category_id' => 100];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH
                    . '?' . http_build_query($data),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetTree',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, $data);

        $expectedData = [
            CategoryTreeInterface::ID => 100,
            CategoryTreeInterface::TITLE => 'Category-title-1',
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => 100,
                CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
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
        ];
        AssertArrayEquals::assert($expectedData, $response, [ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testGetSubTreeWithSearchCriteria()
    {
        $data = ['category_id' => 100];
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
                'resourcePath' => self::RESOURCE_PATH
                    . '?' . http_build_query($data)
                    . '&' . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetTree',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        $expectedData = [
            CategoryTreeInterface::ID => 100,
            CategoryTreeInterface::TITLE => 'Category-title-1',
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => 100,
                CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
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
        ];
        AssertArrayEquals::assert($expectedData, $response, [ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
    }
}
