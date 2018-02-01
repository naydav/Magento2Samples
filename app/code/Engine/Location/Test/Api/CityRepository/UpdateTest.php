<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class UpdateTest extends WebapiAbstract
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
    public function testUpdate()
    {
        $cityId = 100;
        $data = [
            CityInterface::REGION_ID => 200,
            CityInterface::ENABLED => false,
            CityInterface::POSITION => 200,
            CityInterface::NAME => 'City-name-updated',
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $cityId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->_webApiCall($serviceInfo, ['city' => $data]);
        } else {
            $soapData = $data;
            $soapData['cityId'] = $cityId;
            $this->_webApiCall($serviceInfo, ['city' => $soapData]);
        }

        AssertArrayContains::assert(
            $data,
            $this->getCityDataById($cityId)
        );
    }

    /**
     * @param int $cityId
     * @return array
     */
    private function getCityDataById(int $cityId): array
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
