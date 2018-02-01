<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CountryRepository;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class GetTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-location/country';
    const SERVICE_NAME = 'locationCountryRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testGet()
    {
        $countryId = 100;
        $expectedData = [
            CountryInterface::ENABLED => true,
            CountryInterface::POSITION => 100,
            CountryInterface::NAME => 'Country-name-100',
        ];
        $country = $this->getCountryById($countryId);
        AssertArrayContains::assert($expectedData, $country);
    }

    public function testGetNoSuchEntityException()
    {
        $notExistingId = -1;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $notExistingId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];

        $expectedMessage = 'Country with id "%id" does not exist.';
        try {
            (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
                ? $this->_webApiCall($serviceInfo)
                : $this->_webApiCall($serviceInfo, ['countryId' => $notExistingId]);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
                $errorData = $this->processRestExceptionResult($e);
                self::assertEquals($expectedMessage, $errorData['message']);
                self::assertEquals($notExistingId, $errorData['parameters']['id']);
                self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
            } elseif (TESTS_WEB_API_ADAPTER == self::ADAPTER_SOAP) {
                $this->assertInstanceOf('SoapFault', $e);
                $this->checkSoapFault($e, $expectedMessage, 'env:Sender', ['id' => $notExistingId]);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param int $countryId
     * @return array
     */
    private function getCountryById(int $countryId): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $countryId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
            ? $this->_webApiCall($serviceInfo)
            : $this->_webApiCall($serviceInfo, ['countryId' => $countryId]);

        self::assertArrayHasKey(CountryInterface::COUNTRY_ID, $response);
        self::assertEquals($countryId, $response[CountryInterface::COUNTRY_ID]);
        return $response;
    }
}
