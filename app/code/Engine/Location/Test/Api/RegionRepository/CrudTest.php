<?php
namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Framework\Test\AssertArrayContains;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    public function testCreate()
    {
        $data = [
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'Region-title',
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
        $regionId = $this->_webApiCall($serviceInfo, ['region' => $data]);
        self::assertNotEmpty($regionId);

        $region = $this->getRegionById($regionId);
        AssertArrayContains::assert($data, $region);

        /** @var RegionRepositoryInterface $regionRepository */
        $regionRepository = Bootstrap::getObjectManager()->get(
            RegionRepositoryInterface::class
        );
        $regionRepository->deleteById($regionId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $regionId = 100;
        $data = [
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 20,
            RegionInterface::TITLE => 'Region-title-updated',
        ];
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
        $this->_webApiCall($serviceInfo, ['region' => $data], null, 'all');

        $data[RegionInterface::REGION_ID] = $regionId;
        AssertArrayContains::assert(
            $data,
            $this->getRegionById($regionId, 'default')
        );
        AssertArrayContains::assert(
            $data,
            $this->getRegionById($regionId, 'test_store')
        );
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     * @magentoApiDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            RegionInterface::IS_ENABLED => false,
            RegionInterface::TITLE => 'Region-title-per-store',
        ];
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
        $this->_webApiCall($serviceInfo, ['region' => $dataForTestStore], null, $storeCode);

        $region = $this->getRegionById($regionId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            RegionInterface::TITLE => 'Region-title-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $region);

        $region = $this->getRegionById($regionId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $region);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $data = [
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 10,
            RegionInterface::TITLE => null,
        ];
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
        $this->_webApiCall($serviceInfo, ['region' => $data], null, $storeCode);

        $region = $this->getRegionById($regionId, $storeCode);
        $expectedData = [
            RegionInterface::TITLE => 'Region-title-100',
        ];
        AssertArrayContains::assert($expectedData, $region);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testDeleteById()
    {
        $regionId = 100;
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $regionId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        try {
            $this->getRegionById($regionId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals('Region with id "%1" does not exist.', $errorData['message']);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testGet()
    {
        $regionId = 100;
        $expectedData = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'Region-title-100',
        ];
        $region = $this->getRegionById($regionId);
        AssertArrayContains::assert($expectedData, $region);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100_store_scope.php
     */
    public function testGetIfValueIsPerStore()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $expectedData = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'Region-title-100-per-store',
        ];
        $region = $this->getRegionById($regionId, $storeCode);
        AssertArrayContains::assert($expectedData, $region);
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

        $expectedMessage = 'Region with id "%1" does not exist.';
        try {
            $this->_webApiCall($serviceInfo);
            $this->fail('Expected throwing exception');
        } catch (\SoapFault $e) {
            self::assertContains($expectedMessage, $e->getMessage(), 'SoapFault does not contain expected message.');
        } catch (\Exception $e) {
            $errorData = $this->processRestExceptionResult($e);
            self::assertEquals($expectedMessage, $errorData['message']);
            self::assertEquals($notExistingId, $errorData['parameters'][0]);
            self::assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @param int $id
     * @param string|null $storeCode
     * @return array|int|string|float|bool Web API call results
     */
    private function getRegionById($id, $storeCode = null)
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
