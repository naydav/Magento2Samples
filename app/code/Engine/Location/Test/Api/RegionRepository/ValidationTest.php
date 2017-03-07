<?php
namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Location\Api\Data\RegionInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
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
            RegionInterface::IS_ENABLED => false,
            RegionInterface::TITLE => 'Region-title',
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
            $this->_webApiCall($serviceInfo, ['region' => $data], null, 'all');
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
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testFailedValidationOnUpdate($field, $value, array $expectedErrorObj)
    {
        $regionId = 100;
        $data = [
            RegionInterface::IS_ENABLED => false,
            RegionInterface::TITLE => 'Region-title-updated',
        ];
        $data[$field] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $regionId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];

        try {
            $this->_webApiCall($serviceInfo, ['region' => $data], null, 'all');
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
                RegionInterface::TITLE,
                '',
                [
                    'message' => '"%1" can not be empty.',
                    'parameters' => [
                        RegionInterface::TITLE,
                    ],
                ],
            ],
        ];
    }
}
