<?php
namespace Engine\Location\Test\Api\RegionRepository;

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
    const RESOURCE_PATH = '/V1/location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region_list_global_scope_data.php
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

        $this->assertEquals(count($response['items']), $response['total_count']);
        $this->assertTrue(
            $response['search_criteria'] === array_replace_recursive($response['search_criteria'], $searchCriteria)
        );
        $this->assertTrue(
            $response['items'] === array_replace_recursive($response['items'], $expectedItemsData)
        );
    }

    /**
     * @return array
     */
    public function dataProviderGetListIfValueIsInGlobalScope()
    {
        return [
            'filteringByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::IS_ENABLED,
                                    'value' => '0',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::IS_ENABLED => false,
                    ],
                    [
                        RegionInterface::TITLE => 'region-1',
                        RegionInterface::IS_ENABLED => false,
                    ],
                ]
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
                ],
                [
                    [
                        RegionInterface::TITLE => 'region-2',
                    ],
                    [
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
                    ],
                ],
                [
                    [
                        RegionInterface::TITLE => 'region-1',
                        RegionInterface::POSITION => 300,
                    ],
                    [
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'region-3',
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
                    ],
                ],
                [
                    [
                        RegionInterface::TITLE => 'region-3',
                        RegionInterface::POSITION => 100,
                    ],
                    [
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'region-1',
                        RegionInterface::POSITION => 300,
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region_list_store_scope_data.php
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

        $this->assertEquals(count($response['items']), $response['total_count']);
        $this->assertTrue(
            $response['search_criteria'] === array_replace_recursive($response['search_criteria'], $searchCriteria)
        );
        $this->assertTrue(
            $response['items'] === array_replace_recursive($response['items'], $expectedItemsData)
        );
    }

    /**
     * @return array
     */
    public function dataProviderGetListIfValueIsPerStore()
    {
        return [
            'filteringByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RegionInterface::TITLE,
                                    'value' => 'per-store-region-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RegionInterface::TITLE => 'per-store-region-2',
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
                        RegionInterface::TITLE => 'region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'per-store-region-3',
                        RegionInterface::POSITION => 100,
                    ],
                    [
                        RegionInterface::TITLE => 'per-store-region-2',
                        RegionInterface::POSITION => 200,
                    ],
                    [
                        RegionInterface::TITLE => 'per-store-region-1',
                        RegionInterface::POSITION => 300,
                    ],
                ],
            ],
        ];
    }
}
