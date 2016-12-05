<?php
namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Test\AssertArrayContains;
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
    const RESOURCE_PATH = '/V1/location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope_data.php
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

        self::assertEquals(count($response['items']), $response['total_count']);
        AssertArrayContains::assertArrayContains($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assertArrayContains($expectedItemsData, $response['items']);
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
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'region-3',
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
                ],
                [
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::IS_ENABLED => false,
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::IS_ENABLED => false,
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
                                    'value' => 'region-2',
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
                        RegionInterface::TITLE => 'region-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'region-2',
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
                        RegionInterface::REGION_ID => 400,
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
                        RegionInterface::REGION_ID => 100,
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
                        RegionInterface::TITLE => 'region-3',
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'region-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'region-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::TITLE => 'region-1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_store_scope_data.php
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

        self::assertEquals(count($response['items']), $response['total_count']);
        AssertArrayContains::assertArrayContains($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assertArrayContains($expectedItemsData, $response['items']);
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
                                    'value' => 'z-per-store-region-3',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 100,
                        RegionInterface::TITLE => 'z-per-store-region-3',
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
                                    'value' => 'z-per-store-region-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'z-per-store-region-2',
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
                        RegionInterface::TITLE => 'z-per-store-region-3',
                    ],
                    [
                        RegionInterface::REGION_ID => 300,
                        RegionInterface::TITLE => 'z-per-store-region-2',
                    ],
                    [
                        RegionInterface::REGION_ID => 400,
                        RegionInterface::TITLE => 'z-per-store-region-1',
                    ],
                    [
                        RegionInterface::REGION_ID => 200,
                        RegionInterface::TITLE => 'region-2',
                    ],
                ],
            ],
        ];
    }
}
