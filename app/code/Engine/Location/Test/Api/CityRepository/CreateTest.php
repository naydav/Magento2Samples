<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/city';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var int
     */
    private $cityId;

    protected function setUp()
    {
        parent::setUp();
        $this->cityRepository = Bootstrap::getObjectManager()->get(CityRepositoryInterface::class);
    }

    public function testCreate()
    {
        $expectedData = [
            CityInterface::REGION_ID => 100,
            CityInterface::ENABLED => true,
            CityInterface::POSITION => 100,
            CityInterface::NAME => 'City-name',
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
        $cityId = $this->_webApiCall($serviceInfo, ['city' => $expectedData]);

        self::assertNotEmpty($cityId);
        $this->cityId = $cityId;
        AssertArrayContains::assert($expectedData, $this->getCityDataById($cityId));
    }

    protected function tearDown()
    {
        if (null !== $this->cityId) {
            $this->cityRepository->deleteById($this->cityId);
        }
        parent::tearDown();
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
