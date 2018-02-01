<?php
declare(strict_types=1);

namespace Engine\Location\Test\Api\CityRepository;

use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Webapi\Rest\Request;
use Magento\TestFramework\Assert\AssertArrayContains;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
* @author naydav <valeriy.nayda@gmail.com>
*/
class GetListTest extends WebapiAbstract
{
    /**#@+
     * Service constants
     */
    const RESOURCE_PATH = '/V1/engine-location/city';
    const SERVICE_NAME = 'locationCityRepositoryV1';
    /**#@-*/

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Location/Test/_files/cities.php
     */
    public function testGetList()
    {
        $requestData = [
            'searchCriteria' => [
                SearchCriteria::FILTER_GROUPS => [
                    [
                        'filters' => [
                            [
                                'field' => CityInterface::ENABLED,
                                'value' => 0,
                                'condition_type' => 'eq',
                            ],
                        ],
                    ],
                ],
                SearchCriteria::SORT_ORDERS => [
                    [
                        'field' => CityInterface::POSITION,
                        'direction' => SortOrder::SORT_DESC,
                    ],
                ],
                SearchCriteria::CURRENT_PAGE => 2,
                SearchCriteria::PAGE_SIZE => 1,
            ],
        ];
        $expectedTotalCount = 2;
        $expectedItemsData = [
            [
                CityInterface::CITY_ID => 400,
                CityInterface::NAME => 'City-name-3',
            ],
        ];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => self::RESOURCE_PATH . '?' . http_build_query($requestData),
                'httpMethod' => Request::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'operation' => self::SERVICE_NAME . 'GetList',
            ],
        ];
        $response = (TESTS_WEB_API_ADAPTER == self::ADAPTER_REST)
            ? $this->_webApiCall($serviceInfo)
            : $this->_webApiCall($serviceInfo, $requestData);

        AssertArrayContains::assert($requestData['searchCriteria'], $response['search_criteria']);
        self::assertGreaterThanOrEqual($expectedTotalCount, $response['total_count']);
        AssertArrayContains::assert($expectedItemsData, $response['items']);
    }
}
