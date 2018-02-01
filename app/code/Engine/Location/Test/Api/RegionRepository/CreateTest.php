<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CreateTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-location/region';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var int
     */
    private $regionId;

    protected function setUp()
    {
        parent::setUp();
        $this->regionRepository = Bootstrap::getObjectManager()->get(RegionRepositoryInterface::class);
    }

    public function testCreate()
    {
        $expectedData = [
            RegionInterface::COUNTRY_ID => 100,
            RegionInterface::ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::NAME => 'Region-name',
        ];
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
        $regionId = $this->_webApiCall($serviceInfo, ['region' => $expectedData]);

        self::assertNotEmpty($regionId);
        $this->regionId = $regionId;
        AssertArrayContains::assert($expectedData, $this->getRegionDataById($regionId));
    }

    protected function tearDown()
    {
        if (null !== $this->regionId) {
            $this->regionRepository->deleteById($this->regionId);
        }
        parent::tearDown();
    }

    /**
     * @param int $regionId
     * @return array
     */
    private function getRegionDataById(int $regionId): array
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '/' . $regionId,
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'Get',
            ],
        ];
        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
            ? $this->_webApiCall($serviceInfo)
            : $this->_webApiCall($serviceInfo, ['regionId' => $regionId]);

        self::assertArrayHasKey(RegionInterface::REGION_ID, $response);
        self::assertEquals($regionId, $response[RegionInterface::REGION_ID]);
        return $response;
    }
}
