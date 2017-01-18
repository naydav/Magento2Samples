<?php
namespace Engine\Category\Test\Api\CategoryRepository;

use Engine\Framework\Test\AssertArrayContains;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class GetListTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-category/categories';
    const SERVICE_NAME = 'categoryCategoryRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list_global_scope.php
     * @param array $searchCriteria
     * @param array $expectedItemsData
     * @dataProvider dataProviderGetListIfValueIsInGlobalScope
     */
    public function testGetListIfValueIsInGlobalScope(array $searchCriteria, array $expectedItemsData)
    {
        $storeCode = 'test_store';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, [], null, $storeCode);

        self::assertEquals(count($expectedItemsData), $response['total_count']);
        AssertArrayContains::assert($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * @return array
     */
    public function dataProviderGetListIfValueIsInGlobalScope()
    {
        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);

        return [
            'filteringById' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::CATEGORY_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        // check one global and one per store field
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::TITLE => 'Category-title-3',
                    ],
                ],
            ],
            'filteringByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::IS_ENABLED,
                                    'value' => 0,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::IS_ENABLED => false,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::IS_ENABLED => false,
                        CategoryInterface::TITLE => 'Category-title-1',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsGlobal' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::TITLE,
                                    'value' => 'Category-title-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CategoryInterface::CATEGORY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                ],
            ],
            'orderingByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::PARENT_ID,
                                    'value' => $rootCategoryIdProvider->provide(),
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CategoryInterface::POSITION,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => CategoryInterface::CATEGORY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::POSITION => 300,
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::POSITION => 200,
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::POSITION => 200,
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::POSITION => 100,
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsGlobal' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::PARENT_ID,
                                    'value' => $rootCategoryIdProvider->provide(),
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CategoryInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => CategoryInterface::CATEGORY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::TITLE => 'Category-title-3',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::TITLE => 'Category-title-1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list_store_scope.php
     * @param array $searchCriteria
     * @param array $expectedItemsData
     * @dataProvider dataProviderGetListIfValueIsPerStore
     */
    public function testGetListIfValueIsPerStore(array $searchCriteria, array $expectedItemsData)
    {
        $storeCode = 'test_store';
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, [], null, $storeCode);

        self::assertEquals(count($expectedItemsData), $response['total_count']);
        AssertArrayContains::assert($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * @return array
     */
    public function dataProviderGetListIfValueIsPerStore()
    {
        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);

        return [
            'filteringByIdAndPerStoreFieldValue' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::CATEGORY_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::TITLE,
                                    'value' => 'z-sort-Category-title-3-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::TITLE => 'z-sort-Category-title-3-per-store',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::TITLE,
                                    'value' => 'z-sort-Category-title-2-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::TITLE => 'z-sort-Category-title-2-per-store',
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CategoryInterface::PARENT_ID,
                                    'value' => $rootCategoryIdProvider->provide(),
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CategoryInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::TITLE => 'z-sort-Category-title-3-per-store',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 300,
                        CategoryInterface::TITLE => 'z-sort-Category-title-2-per-store',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::TITLE => 'z-sort-Category-title-1-per-store',
                    ],
                    [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                ],
            ],
        ];
    }
}
