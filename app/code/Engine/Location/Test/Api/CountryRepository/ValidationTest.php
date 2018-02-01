<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CountryRepository;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class ValidationTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-location/country';
    const SERVICE_NAME = 'locationCountryRepositoryV1';
    /**#@-*/

    /**
     * @var array
     */
    private $validData = [
        CountryInterface::ENABLED => false,
        CountryInterface::POSITION => 100,
        CountryInterface::NAME => 'Country-name',
    ];

    /**
     * @param string $field
     * @param array $expectedErrorData
     * @throws \Exception
     * @dataProvider dataProviderRequiredFields
     */
    public function testCreateWithMissedRequiredFields(string $field, array $expectedErrorData)
    {
        $data = $this->validData;
        unset($data[$field]);

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->webApiCall($serviceInfo, $data, $expectedErrorData);
    }

    /**
     * @return array
     */
    public function dataProviderRequiredFields(): array
    {
        return [
            'without_' . CountryInterface::NAME => [
                CountryInterface::NAME,
                [
                    'message' => 'Validation Failed',
                    'errors' => [
                        [
                            'message' => '"%field" can not be empty.',
                            'parameters' => [
                                'field' => CountryInterface::NAME,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param array $expectedErrorData
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidationOnCreate(string $field, $value, array $expectedErrorData)
    {
        $data = $this->validData;
        $data[$field] = $value;

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->webApiCall($serviceInfo, $data, $expectedErrorData);
    }

    /**
     * @param string $field
     * @param string|null $value
     * @param array $expectedErrorData
     * @dataProvider failedValidationDataProvider
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testFailedValidationOnUpdate(string $field, $value, array $expectedErrorData)
    {
        $data = $this->validData;
        $data[$field] = $value;

        $countryId = 10;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $countryId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->webApiCall($serviceInfo, $data, $expectedErrorData);
    }

    /**
     * @return array
     */
    public function failedValidationDataProvider(): array
    {
        return [
            'empty_' . CountryInterface::NAME => [
                CountryInterface::NAME,
                '',
                [
                    'message' => 'Validation Failed',
                    'errors' => [
                        [
                            'message' => '"%field" can not be empty.',
                            'parameters' => [
                                'field' => CountryInterface::NAME,
                            ],
                        ],
                    ],
                ],
            ],
            'whitespaces_' . CountryInterface::NAME => [
                CountryInterface::NAME,
                ' ',
                [
                    'message' => 'Validation Failed',
                    'errors' => [
                        [
                            'message' => '"%field" can not be empty.',
                            'parameters' => [
                                'field' => CountryInterface::NAME,
                            ],
                        ],
                    ],
                ],
            ],
            'null_' . CountryInterface::NAME => [
                CountryInterface::NAME,
                null,
                [
                    'message' => 'Validation Failed',
                    'errors' => [
                        [
                            'message' => '"%field" can not be empty.',
                            'parameters' => [
                                'field' => CountryInterface::NAME,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $serviceInfo
     * @param array $data
     * @param array $expectedErrorData
     * @return void
     * @throws \Exception
     */
    private function webApiCall(array $serviceInfo, array $data, array $expectedErrorData)
    {
        try {
            $this->_webApiCall($serviceInfo, ['country' => $data]);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
                self::assertEquals($expectedErrorData, $this->processRestExceptionResult($e));
                self::assertEquals(Exception::HTTP_BAD_REQUEST, $e->getCode());
            } elseif (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $expectedWrappedErrors = [];
                foreach ($expectedErrorData['errors'] as $error) {
                    // @see \Magento\TestFramework\TestCase\WebapiAbstract::getActualWrappedErrors()
                    $expectedWrappedErrors[] = [
                        'message' => $error['message'],
                        'params' => $error['parameters'],
                    ];
                }
                $this->checkSoapFault($e, $expectedErrorData['message'], 'env:Sender', [], $expectedWrappedErrors);
            } else {
                throw $e;
            }
        }
    }
}
