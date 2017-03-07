<?php
namespace Engine\CharacteristicGroup\Test\Api\CharacteristicGroupRepository;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
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
    const RESOURCE_PATH = '/V1/engine-characteristic-group/characteristic-groups';
    const SERVICE_NAME = 'characteristicGroupCharacteristicGroupRepositoryV1';
    /**#@-*/

    /**
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidationOnCreate($field, $value, array $expectedErrorObj)
    {
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
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
            $this->_webApiCall($serviceInfo, ['characteristicGroup' => $data], null, 'all');
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
     * @param string $field
     * @param mixed $value
     * @param array $expectedErrorObj
     * @dataProvider failedValidationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testFailedValidationOnUpdate($field, $value, array $expectedErrorObj)
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-updated',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-updated',
        ];
        $data[$field] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $characteristicGroupId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        try {
            $this->_webApiCall($serviceInfo, ['characteristicGroup' => $data], null, 'all');
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
            'empty_title' => [
                CharacteristicGroupInterface::TITLE,
                '',
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        CharacteristicGroupInterface::TITLE,
                    ],
                ],
            ],
        ];
    }
}
