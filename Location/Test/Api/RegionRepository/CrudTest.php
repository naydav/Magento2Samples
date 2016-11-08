<?php
namespace Engine\Location\Test\Api\RegionRepository;

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
    const RESOURCE_PATH = '/V1/location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    public function testCreate()
    {
        $data = [
            RegionInterface::TITLE => 'region-title',
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 10,
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
        $this->assertNotEmpty($regionId);

        $region = $this->getRegion($regionId);
        $this->checkRegion($data, $region);

        /** @var RegionRepositoryInterface $regionRepository */
        $regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
        $regionRepository->deleteById($regionId);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $regionId = 100;
        $data = [
            RegionInterface::TITLE => 'region-title-updated',
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 20,
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

        $this->checkRegion($data, $this->getRegion($regionId, 'default'));
        $this->checkRegion($data, $this->getRegion($regionId, 'test_store'));
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $title = 'region-title-per-store';
        $data = [
            RegionInterface::TITLE => $title,
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

        $region = $this->getRegion($regionId, 'default');
        $this->assertEquals('title-0', $region[RegionInterface::TITLE]);

        $region = $this->getRegion($regionId, $storeCode);
        $this->assertEquals($title, $region[RegionInterface::TITLE]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
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
            $this->getRegion($regionId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals('Region with id "%1" does not exist.', $errorObj['message']);
            $this->assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region_store_scope_data.php
     */
    public function testGetIfValueIsPerStore()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $expectedData = [
            RegionInterface::TITLE => 'per-store-title-0',
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 1000,
        ];
        $region = $this->getRegion($regionId, $storeCode);
        $this->checkRegion($expectedData, $region);
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

        $expectedMessage = "Region with id \"%1\" does not exist.";
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
    protected function checkRegion($expected, $actual)
    {
        $this->assertEquals($expected[RegionInterface::TITLE], $actual[RegionInterface::TITLE]);
        $this->assertEquals($expected[RegionInterface::IS_ENABLED], $actual[RegionInterface::IS_ENABLED]);
        $this->assertEquals($expected[RegionInterface::POSITION], $actual[RegionInterface::POSITION]);
    }

    /**
     * @param int $id
     * @param string|null $storeCode
     * @return array|int|string|float|bool Web API call results
     */
    protected function getRegion($id, $storeCode = null)
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
