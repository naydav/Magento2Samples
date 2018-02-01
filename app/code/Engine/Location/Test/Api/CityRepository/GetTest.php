<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/city';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city.php
     */
    public function testGet()
    {
        $cityId = 100;
        $expectedData = [
            CityInterface::REGION_ID => 100,
            CityInterface::ENABLED => true,
            CityInterface::POSITION => 100,
            CityInterface::NAME => 'City-name-100',
        ];
        $city = $this->getCityById($cityId);
        AssertArrayContains::assert($expectedData, $city);
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

        $expectedMessage = 'City with id "%id" does not exist.';
        try {
            (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
                ? $this->_webApiCall($serviceInfo)
                : $this->_webApiCall($serviceInfo, ['cityId' => $notExistingId]);
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
     * @param int $cityId
     * @return array
     */
    private function getCityById(int $cityId): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $cityId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
            ? $this->_webApiCall($serviceInfo)
            : $this->_webApiCall($serviceInfo, ['cityId' => $cityId]);

        self::assertArrayHasKey(CityInterface::CITY_ID, $response);
        self::assertEquals($cityId, $response[CityInterface::CITY_ID]);
        return $response;
    }
}
