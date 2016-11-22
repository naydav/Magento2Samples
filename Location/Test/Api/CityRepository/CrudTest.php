<?php
namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Webapi\Exception;
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
    const RESOURCE_PATH = '/V1/location/cities';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    public function testCreate()
    {
        $data = [
            CityInterface::TITLE => 'city-title',
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 10,
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
        $cityId = $this->_webApiCall($serviceInfo, ['city' => $data]);
        $this->assertNotEmpty($cityId);

        $city = $this->getCity($cityId);
        $this->checkCity($data, $city);

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
        $cityRepository->deleteById($cityId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $cityId = 100;
        $data = [
            CityInterface::TITLE => 'city-title-updated',
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 20,
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
        $this->_webApiCall($serviceInfo, ['city' => $data], null, 'all');

        $this->checkCity($data, $this->getCity($cityId, 'default'));
        $this->checkCity($data, $this->getCity($cityId, 'test_store'));
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $title = 'city-title-per-store';
        $data = [
            CityInterface::TITLE => $title,
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
        $this->_webApiCall($serviceInfo, ['city' => $data], null, $storeCode);

        $city = $this->getCity($cityId, 'default');
        $this->assertEquals('title-0', $city[CityInterface::TITLE]);

        $city = $this->getCity($cityId, $storeCode);
        $this->assertEquals($title, $city[CityInterface::TITLE]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope_data.php
     */
    public function testDeleteValueInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $data = [
            CityInterface::TITLE => null,
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
        $this->_webApiCall($serviceInfo, ['city' => $data], null, $storeCode);

        $city = $this->getCity($cityId, $storeCode);
        $this->assertEquals('title-0', $city[CityInterface::TITLE]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testDeleteById()
    {
        $cityId = 100;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $cityId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        try {
            $this->getCity($cityId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals('City with id "%1" does not exist.', $errorObj['message']);
            $this->assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope_data.php
     */
    public function testGetIfValueIsPerStore()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $expectedData = [
            CityInterface::TITLE => 'per-store-title-0',
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 1000,
        ];
        $city = $this->getCity($cityId, $storeCode);
        $this->checkCity($expectedData, $city);
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

        $expectedMessage = "City with id \"%1\" does not exist.";
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            $this->assertContains($expectedMessage, $e->getMessage(), 'SoapFault does not contain expected message.');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals($expectedMessage, $errorObj['message']);
            $this->assertEquals($notExistingId, $errorObj['parameters'][0]);
            $this->assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param array $expected
     * @param array $actual
     */
    protected function checkCity($expected, $actual)
    {
        $this->assertEquals($expected[CityInterface::TITLE], $actual[CityInterface::TITLE]);
        $this->assertEquals($expected[CityInterface::IS_ENABLED], $actual[CityInterface::IS_ENABLED]);
        $this->assertEquals($expected[CityInterface::POSITION], $actual[CityInterface::POSITION]);
    }

    /**
     * @param int $id
     * @param string|null $storeCode
     * @return array|int|string|float|bool Web API call results
     */
    protected function getCity($id, $storeCode = null)
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $id,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo, [], null, $storeCode);
        return $response;
    }
}
