<?php
namespace Engine\Location\Test\Api;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionRepositoryTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/location/regions';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @var int
     */
    private static $regionId;

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
        $response = $this->_webApiCall($serviceInfo, ['region' => $data]);
        $this->assertArrayHasKey(RegionInterface::REGION_ID, $response);

        $region = $this->getRegion($response[RegionInterface::REGION_ID]);
        $this->checkRegion($data, $region);
        self::$regionId = $region[RegionInterface::REGION_ID];
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-updated',
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 20,
        ];
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . self::$regionId,
                'httpMethod' => Request::HTTP_METHOD_PUT,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Save',
            ],
        ];
        $this->_webApiCall($serviceInfo, ['region' => $data]);
        
        $region = $this->getRegion(self::$regionId);
        $this->checkRegion($data, $region);
    }

    /**
     * @depends testUpdate
     */
    public function testDeleteById()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . self::$regionId,
                'httpMethod' => Request::HTTP_METHOD_DELETE,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'DeleteById',
            ],
        ];
        $this->_webApiCall($serviceInfo);

        try {
            $this->getRegion(self::$regionId);
            $this->fail('Expected throwing exception');
        } catch (\Exception $e) {
            $errorObj = $this->processRestExceptionResult($e);
            $this->assertEquals('Region with id "%1" does not exist.', $errorObj['message']);
            $this->assertEquals(Exception::HTTP_NOT_FOUND, $e->getCode());
        }
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region_list.php
     */
    public function testGetList()
    {
        $searchCriteria = [
            'searchCriteria' => [
                'filter_groups' => [
                    [
                        'filters' => [
                            [
                                'field' => RegionInterface::POSITION,
                                'value' => 200,
                                'condition_type' => 'eq',
                            ],
                        ],
                    ],
                ],
                'current_page' => 1,
                'page_size' => 2,
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($searchCriteria),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        $this->assertEquals($searchCriteria['searchCriteria'], $response['search_criteria']);
        $this->assertEquals(2, $response['total_count']);
        $this->assertEquals(200, $response['items'][0][RegionInterface::POSITION]);
        $this->assertEquals(200, $response['items'][1][RegionInterface::POSITION]);
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region_list.php
     */
    public function testGetListByPerStoreField()
    {
        $searchCriteria = [
            'searchCriteria' => [
                'filter_groups' => [
                    [
                        'filters' => [
                            [
                                'field' => RegionInterface::TITLE,
                                'value' => 'region-aa',
                                'condition_type' => 'eq',
                            ],
                        ],
                    ],
                ],
                'current_page' => 1,
                'page_size' => 2,
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($searchCriteria),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = $this->_webApiCall($serviceInfo);

        $this->assertEquals($searchCriteria['searchCriteria'], $response['search_criteria']);
        $this->assertEquals(2, $response['total_count']);
        $this->assertEquals('region-aa', $response['items'][0][RegionInterface::TITLE]);
        $this->assertEquals('region-aa', $response['items'][1][RegionInterface::TITLE]);
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
     * @return array|int|string|float|bool Web API call results
     */
    protected function getRegion($id)
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
        $response = $this->_webApiCall($serviceInfo);
        return $response;
    }
}
