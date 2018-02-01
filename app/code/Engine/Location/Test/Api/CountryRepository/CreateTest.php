<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CountryRepository;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\CountryRepositoryInterface;
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
    const RESOURCE_PATH = '/V1/engine-location/country';
    const SERVICE_NAME = 'locationCountryRepositoryV1';
    /**#@-*/

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var int
     */
    private $countryId;

    protected function setUp()
    {
        parent::setUp();
        $this->countryRepository = Bootstrap::getObjectManager()->get(CountryRepositoryInterface::class);
    }

    public function testCreate()
    {
        $expectedData = [
            CountryInterface::ENABLED => true,
            CountryInterface::POSITION => 100,
            CountryInterface::NAME => 'Country-name',
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
        $countryId = $this->_webApiCall($serviceInfo, ['country' => $expectedData]);

        self::assertNotEmpty($countryId);
        $this->countryId = $countryId;
        AssertArrayContains::assert($expectedData, $this->getCountryDataById($countryId));
    }

    protected function tearDown()
    {
        if (null !== $this->countryId) {
            $this->countryRepository->deleteById($this->countryId);
        }
        parent::tearDown();
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
