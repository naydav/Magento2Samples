<?php
namespace Engine\CharacteristicGroup\Test\Api\RelationRepository;

use Engine\CharacteristicGroup\Api\Data\RelationInterface;
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
    const RESOURCE_PATH = '/V1/engine-characteristic-group-characteristic/relations';
    const SERVICE_NAME = 'characteristicGroupCharacteristicRelationRepositoryV1';
    /**#@-*/

    /**
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider failedValidationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_id_100.php
     */
    public function testFailedValidationOnCreate($field, $value, array $expectedErrorObj)
    {
        $data = [
            RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
            RelationInterface::CHARACTERISTIC_ID => 100,
            RelationInterface::CHARACTERISTIC_POSITION => 1,
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
    public function failedValidationDataProvider()
    {
        return [
            'empty_characteristic_group_id' => [
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                null,
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        RelationInterface::CHARACTERISTIC_GROUP_ID,
                    ],
                ],
            ],
            'empty_characteristi_id' => [
                RelationInterface::CHARACTERISTIC_ID,
                null,
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        RelationInterface::CHARACTERISTIC_ID,
                    ],
                ],
            ],
            'not_exist_characteristic_group_id' => [
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                -1,
                [
                    'message' => 'Characteristic Group with id "%1" is not found.',
                    'parameters' => [
                        -1,
                    ],
                ],
            ],
            'not_exist_characteristic_id' => [
                RelationInterface::CHARACTERISTIC_ID,
                -1,
                [
                    'message' => 'Characteristic with id "%1" is not found.',
                    'parameters' => [
                        -1,
                    ],
                ],
            ],
        ];
    }
}
