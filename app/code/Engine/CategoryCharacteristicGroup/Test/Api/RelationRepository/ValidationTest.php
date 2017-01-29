<?php
namespace Engine\CategoryCharacteristicGroup\Test\Api\RelationRepository;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class ValidationTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-category-characteristic-group/relations';
    const SERVICE_NAME = 'categoryCharacteristicGroupRelationRepositoryV1';
    /**#@-*/

    /**
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider validationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testValidationOnCreate($field, $value, array $expectedErrorObj)
    {
        $data = [
            RelationInterface::CATEGORY_ID => 100,
            RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
            RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
        ];
        $data[$field] = $value;

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

        try {
            $this->_webApiCall($serviceInfo, ['relation' => $data]);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains(
                $expectedErrorObj['message'],
                $e->getMessage(),
                'SoapFault does not contain expected message.'
            );
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedErrorObj, $errorData);
            self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
        }
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            [
                RelationInterface::CATEGORY_ID,
                null,
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        RelationInterface::CATEGORY_ID,
                    ],
                ],
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                null,
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        RelationInterface::CHARACTERISTIC_GROUP_ID,
                    ],
                ],
            ],
            [
                RelationInterface::CATEGORY_ID,
                -1,
                [
                    'message' => 'Category with id "%1" is not found.',
                    'parameters' => [
                        -1,
                    ],
                ],
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                -1,
                [
                    'message' => 'Characteristic Group with id "%1" is not found.',
                    'parameters' => [
                        -1,
                    ],
                ],
            ],
        ];
    }
}
