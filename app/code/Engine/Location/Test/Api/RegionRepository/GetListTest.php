<?php
namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Framework\Test\AssertArrayContains;
use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class GetListTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope.php
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
        return [
            'filteringById' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::REGION_ID,
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
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'Region-title-3',
                    ],
                ],
            ],
            'filteringByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::IS_ENABLED,
                                    'value' => 0,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => RegionInterface::REGION_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::IS_ENABLED => false,
                        RegionInterface::TITLE => 'Region-title-1',
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::IS_ENABLED => false,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsGlobal' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::TITLE,
                                    'value' => 'Region-title-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => RegionInterface::REGION_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                ],
            ],
            'orderingByNotScopedField' => [
                [
                    'sort_orders' => [
                        [
                            'field' => RegionInterface::POSITION,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => RegionInterface::REGION_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::POSITION => 300,
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::POSITION => 100,
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsGlobal' => [
                [
                    'sort_orders' => [
                        [
                            'field' => RegionInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => RegionInterface::REGION_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'Region-title-3',
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::TITLE => 'Region-title-1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_store_scope.php
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
        return [
            'filteringByIdAndPerStoreFieldValue' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::REGION_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::TITLE,
                                    'value' => 'z-sort-Region-title-3-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'z-sort-Region-title-3-per-store',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::TITLE,
                                    'value' => 'z-sort-Region-title-2-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'z-sort-Region-title-2-per-store',
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsPerStore' => [
                [
                    'sort_orders' => [
                        [
                            'field' => RegionInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'z-sort-Region-title-3-per-store',
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'z-sort-Region-title-2-per-store',
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::TITLE => 'z-sort-Region-title-1-per-store',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'Region-title-2',
                    ],
                ],
            ],
        ];
    }
}
