<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\RegionRepository;

use Engine\Location\Api\Data\RegionInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/region';
    const SERVICE_NAME = 'locationRegionRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
     */
    public function testUpdate()
    {
        $regionId = 100;
        $data = [
            RegionInterface::COUNTRY_ID => 200,
            RegionInterface::ENABLED => false,
            RegionInterface::POSITION => 200,
            RegionInterface::NAME => 'Region-name-updated',
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
        if (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST) {
            $this->_webApiCall($serviceInfo, ['region' => $data]);
        } else {
            $soapData = $data;
            $soapData['regionId'] = $regionId;
            $this->_webApiCall($serviceInfo, ['region' => $soapData]);
        }

        AssertArrayContains::assert(
            $data,
            $this->getRegionDataById($regionId)
        );
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
