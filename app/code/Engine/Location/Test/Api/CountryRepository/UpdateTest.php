<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CountryRepository;

use Engine\Location\Api\Data\CountryInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/country';
    const SERVICE_NAME = 'locationCountryRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testUpdate()
    {
        $countryId = 100;
        $data = [
            CountryInterface::ENABLED => false,
            CountryInterface::POSITION => 200,
            CountryInterface::NAME => 'Country-name-updated',
        ];
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
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->_webApiCall($serviceInfo, ['country' => $data]);
        } else {
            $soapData = $data;
            $soapData['countryId'] = $countryId;
            $this->_webApiCall($serviceInfo, ['country' => $soapData]);
        }

        AssertArrayContains::assert(
            $data,
            $this->getCountryDataById($countryId)
        );
    }

    /**
     * @param int $countryId
     * @return array
     */
    private function getCountryDataById(int $countryId): array
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
