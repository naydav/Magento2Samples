<?php
namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Test\AssertArrayContains;
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

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testCreate()
    {
        $data = [
            CityInterface::REGION_ID => 100,
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
        self::assertNotEmpty($cityId);

        $city = $this->getCityById($cityId);
        AssertArrayContains::assertArrayContains($data, $city);

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
        $cityRepository->deleteById($cityId);
    }

    public function testCreateWithoutRegion()
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
        self::assertNotEmpty($cityId);

        $city = $this->getCityById($cityId);
        AssertArrayContains::assertArrayContains($data, $city);

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
        $cityRepository->deleteById($cityId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_200.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $cityId = 100;
        $data = [
            CityInterface::REGION_ID => 200,
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

        $data[CityInterface::CITY_ID] = $cityId;
        AssertArrayContains::assertArrayContains($data, $this->getCityById($cityId, 'default'));
        AssertArrayContains::assertArrayContains($data, $this->getCityById($cityId, 'test_store'));
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

        $city = $this->getCityById($cityId, 'default');
        self::assertEquals('title-0', $city[CityInterface::TITLE]);

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals($title, $city[CityInterface::TITLE]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope.php
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

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals('title-0', $city[CityInterface::TITLE]);
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
            $this->getCityById($cityId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            self::assertEquals('City with id "%1" does not exist.', $errorObj['message']);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testGet()
    {
        $cityId = 100;
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 100,
            CityInterface::TITLE => 'title-0',
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 200,
        ];
        $city = $this->getCityById($cityId);
        AssertArrayContains::assertArrayContains($data, $city);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope.php
     */
    public function testGetIfValueIsPerStore()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 100,
            CityInterface::TITLE => 'per-store-title-0',
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 200,
        ];
        $city = $this->getCityById($cityId, $storeCode);
        AssertArrayContains::assertArrayContains($data, $city);
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
            self::assertContains($expectedMessage, $e->getMessage(), 'SoapFault does not contain expected message.');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            self::assertEquals($expectedMessage, $errorObj['message']);
            self::assertEquals($notExistingId, $errorObj['parameters'][0]);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param int $id
     * @param string|null $storeCode
     * @return array|int|string|float|bool Web API call results
     */
    private function getCityById($id, $storeCode = null)
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
