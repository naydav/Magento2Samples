<?php
namespace Engine\CategoryCharacteristicGroup\Test\Api\RelationRepository;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Engine\CategoryCharacteristicGroup\Api\RelationRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CrudTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-category-characteristic-group/relations';
    const SERVICE_NAME = 'categoryCharacteristicGroupRelationRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testCreate()
    {
        $data = [
            RelationInterface::CATEGORY_ID => 100,
            RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
            RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $relationId = $this->_webApiCall($serviceInfo, ['relation' => $data], null, 'all');
        self::assertNotEmpty($relationId);

        $relation = $this->getRelationById($relationId);
        AssertArrayContains::assert($data, $relation);

        /** @var RelationRepositoryInterface $relationRepository */
        $relationRepository = Bootstrap::getObjectManager()->get(RelationRepositoryInterface::class);
        $relationRepository->deleteById($relationId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testDeleteById()
    {
        $relationId = 100;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $relationId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        $relation = $this->getRelationById($relationId);
        $this->assertNull($relation);
    }

    /**
     * @param int $id
     * @return array|int|string|float|bool Web API call results
     */
    private function getRelationById($id)
    {
        $searchCriteria = [
            'filter_groups' => [
                [
                    'filters' => [
                        [
                            'field' => RelationInterface::RELATION_ID,
                            'value' => $id,
                            'condition_type' => 'eq',
                        ],
                    ],
                ],
            ],
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query(['searchCriteria' => $searchCriteria]),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);
        return count($response['items']) > 0 ? $response['items'][0] : null;
    }
}
