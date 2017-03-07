<?php
namespace Engine\CharacteristicGroup\Test\Api\RelationRepository;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Engine\Test\AssertArrayContains;
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
    const RESOURCE_PATH = '/V1/engine-characteristic-group-characteristic/relations';
    const SERVICE_NAME = 'characteristicGroupCharacteristicRelationRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     * @param array $searchCriteria
     * @param array $expectedItemsData
     * @dataProvider dataProviderGetList
     */
    public function testGetList(array $searchCriteria, array $expectedItemsData)
    {
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
        $response = $this->_webApiCall($serviceInfo);

        self::assertEquals(count($expectedItemsData), $response['total_count']);
        AssertArrayContains::assert($searchCriteria, $response['search_criteria']);
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }

    /**
     * @return array
     */
    public function dataProviderGetList()
    {
        return [
            'filteringById' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RelationInterface::RELATION_ID,
                                    'value' => 100,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RelationInterface::RELATION_ID => 100,
                    ],
                ],
            ],
            'filteringByCharacteristicGroupId' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RelationInterface::CHARACTERISTIC_GROUP_ID,
                                    'value' => 200,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RelationInterface::RELATION_ID => 200,
                        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                        RelationInterface::CHARACTERISTIC_ID => 200,
                        RelationInterface::CHARACTERISTIC_POSITION => 1,
                    ],
                    [
                        RelationInterface::RELATION_ID => 100,
                        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                        RelationInterface::CHARACTERISTIC_ID => 100,
                        RelationInterface::CHARACTERISTIC_POSITION => 2,
                    ],
                ],
            ],
            'filteringByCharacteristicId' => [
                [
                    'filter_groups' => [
                        [
                            'filters' => [
                                [
                                    'field' => RelationInterface::CHARACTERISTIC_ID,
                                    'value' => 200,
                                    'condition_type' => 'eq',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    [
                        RelationInterface::RELATION_ID => 200,
                        RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                        RelationInterface::CHARACTERISTIC_ID => 200,
                    ],
                    [
                        RelationInterface::RELATION_ID => 300,
                        RelationInterface::CHARACTERISTIC_GROUP_ID => 300,
                        RelationInterface::CHARACTERISTIC_ID => 200,
                    ],
                ],
            ],
        ];
    }
}
