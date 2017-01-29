<?php
namespace Engine\CharacteristicGroup\Test\Api\CharacteristicGroupRepository;

use Engine\Test\AssertArrayContains;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
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
    const RESOURCE_PATH = '/V1/engine-characteristic-group/characteristic-groups';
    const SERVICE_NAME = 'characteristicGroupCharacteristicGroupRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list_global_scope.php
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
                                    'field' => CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
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
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-3',
                    ],
                ],
            ],
            'filteringByNotScopedField' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CharacteristicGroupInterface::IS_ENABLED,
                                    'value' => 0,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::IS_ENABLED => false,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 400,
                        CharacteristicGroupInterface::IS_ENABLED => false,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-1',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsGlobal' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CharacteristicGroupInterface::TITLE,
                                    'value' => 'CharacteristicGroup-title-2',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                    'sort_orders' => [
                        [
                            'field' => CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                ],
            ],
            'orderingByNotScopedField' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CharacteristicGroupInterface::BACKEND_TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-3',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 400,
                        CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-1',
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsGlobal' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CharacteristicGroupInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                        [
                            'field' => CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-3',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 400,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-1',
                    ],
                ],
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list_store_scope.php
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
                                    'field' => CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                        [
                            'filters' => [
                                [
                                    'field' => CharacteristicGroupInterface::TITLE,
                                    'value' => 'z-sort-CharacteristicGroup-title-3-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                        CharacteristicGroupInterface::TITLE => 'z-sort-CharacteristicGroup-title-3-per-store',
                    ],
                ],
            ],
            'filteringByScopedFieldIfValueIsPerStore' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => CharacteristicGroupInterface::TITLE,
                                    'value' => 'z-sort-CharacteristicGroup-title-2-per-store',
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::TITLE => 'z-sort-CharacteristicGroup-title-2-per-store',
                    ],
                ],
            ],
            'orderingByScopedFieldIfValueIsPerStore' => [
                [
                    'sort_orders' => [
                        [
                            'field' => CharacteristicGroupInterface::TITLE,
                            'direction' => SortOrder::SORT_DESC,
                        ],
                    ],
                ],
                [
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                        CharacteristicGroupInterface::TITLE => 'z-sort-CharacteristicGroup-title-3-per-store',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                        CharacteristicGroupInterface::TITLE => 'z-sort-CharacteristicGroup-title-2-per-store',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 400,
                        CharacteristicGroupInterface::TITLE => 'z-sort-CharacteristicGroup-title-1-per-store',
                    ],
                    [
                        CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                        CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-2',
                    ],
                ],
            ],
        ];
    }
}
