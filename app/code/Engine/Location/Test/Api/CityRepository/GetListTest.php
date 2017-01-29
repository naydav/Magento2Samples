<?php
namespace Engine\Location\Test\Api\CityRepository;

use Engine\Test\AssertArrayContains;
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
    const RESOURCE_PATH = '/V1/engine-location/cities';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
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
                                    'field' => CityInterface::CITY_ID,
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
                        CityInterface::CITY_ID => 100,
                        CityInterface::TITLE => 'City-title-3',
                    ],
                ],
            ],
            'filteringByRegionId' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::REGION_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CityInterface::CITY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 200,
                        CityInterface::REGION_ID => 100,
                    ],
                    [
                        CityInterface::CITY_ID => 100,
                        CityInterface::REGION_ID => 100,
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
                                    'value' => 0,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CityInterface::CITY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 400,
                        CityInterface::IS_ENABLED => false,
                        CityInterface::TITLE => 'City-title-1',
                    ],
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::IS_ENABLED => false,
                        CityInterface::TITLE => 'City-title-2',
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
                                    'value' => 'City-title-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CityInterface::CITY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::TITLE => 'City-title-2',
                    ],
                    [
                        CityInterface::CITY_ID => 200,
                        CityInterface::TITLE => 'City-title-2',
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
                        [
                            'field' => CityInterface::CITY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 100,
                        CityInterface::POSITION => 300,
                    ],
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::CITY_ID => 200,
                        CityInterface::POSITION => 200,
                    ],
                    [
                        CityInterface::CITY_ID => 400,
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
                        [
                            'field' => CityInterface::CITY_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 100,
                        CityInterface::TITLE => 'City-title-3',
                    ],
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::TITLE => 'City-title-2',
                    ],
                    [
                        CityInterface::CITY_ID => 200,
                        CityInterface::TITLE => 'City-title-2',
                    ],
                    [
                        CityInterface::CITY_ID => 400,
                        CityInterface::TITLE => 'City-title-1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_store_scope.php
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
                                    'field' => CityInterface::CITY_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                        [
                            'filters' => [
                                [
                                    'field' => CityInterface::TITLE,
                                    'value' => 'z-sort-City-title-3-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 100,
                        CityInterface::TITLE => 'z-sort-City-title-3-per-store',
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
                                    'value' => 'z-sort-City-title-2-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::TITLE => 'z-sort-City-title-2-per-store',
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
                        CityInterface::CITY_ID => 100,
                        CityInterface::TITLE => 'z-sort-City-title-3-per-store',
                    ],
                    [
                        CityInterface::CITY_ID => 300,
                        CityInterface::TITLE => 'z-sort-City-title-2-per-store',
                    ],
                    [
                        CityInterface::CITY_ID => 400,
                        CityInterface::TITLE => 'z-sort-City-title-1-per-store',
                    ],
                    [
                        CityInterface::CITY_ID => 200,
                        CityInterface::TITLE => 'City-title-2',
                    ],
                ],
            ],
        ];
    }
}
