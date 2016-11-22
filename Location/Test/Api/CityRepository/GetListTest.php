<?php
namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
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
    const RESOURCE_PATH = '/V1/location/cities';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
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
        $this->assertArrayContains($searchCriteria, $response['search_criteria']);
        $this->assertArrayContains($expectedItemsData, $response['items']);
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
                                    'field' => CityInterface::CITY_ID,
                                    'value' => '100',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'city-3',
                        CityInterface::IS_ENABLED => true,
                    ],
                ],
            ],
            'filteringByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::IS_ENABLED,
                                    'value' => '0',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::IS_ENABLED => false,
                    ],
                    [
                        CityInterface::TITLE => 'city-1',
                        CityInterface::IS_ENABLED => false,
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsGlobal' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::TITLE,
                                    'value' => 'city-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'city-2',
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                    ],
                ],
            ],
            'orderingByNotScopedField' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CityInterface::POSITION,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'city-1',
                        CityInterface::POSITION => 300,
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::TITLE => 'city-3',
                        CityInterface::POSITION => 100,
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsGlobal' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CityInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'city-3',
                        CityInterface::POSITION => 100,
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::TITLE => 'city-1',
                        CityInterface::POSITION => 300,
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_store_scope_data.php
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
        $this->assertArrayContains($searchCriteria, $response['search_criteria']);
        $this->assertArrayContains($expectedItemsData, $response['items']);
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
                                    'field' => CityInterface::CITY_ID,
                                    'value' => '100',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::TITLE,
                                    'value' => 'z-per-store-city-3',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'z-per-store-city-3',
                        CityInterface::IS_ENABLED => true,
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::TITLE,
                                    'value' => 'z-per-store-city-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'z-per-store-city-2',
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsPerStore' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CityInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::TITLE => 'z-per-store-city-3',
                        CityInterface::POSITION => 100,
                    ],
                    [
                        CityInterface::TITLE => 'z-per-store-city-2',
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::TITLE => 'z-per-store-city-1',
                        CityInterface::POSITION => 300,
                    ],
                    [
                        CityInterface::TITLE => 'city-2',
                        CityInterface::POSITION => 200,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $actual
     * @param array $expected
     * @return void
     */
    private function assertArrayContains(array $actual, array $expected)
    {
        foreach (array_keys($actual) as $dataKey) {
            if (is_array($actual[$dataKey])) {
                $this->assertArrayContains($actual[$dataKey], $expected[$dataKey]);
            } else {
                $this->assertEquals(
                    $expected[$dataKey],
                    $actual[$dataKey],
                    "Expected value for key '{$dataKey}' doesn't match"
                );
            }
        }
    }
}
