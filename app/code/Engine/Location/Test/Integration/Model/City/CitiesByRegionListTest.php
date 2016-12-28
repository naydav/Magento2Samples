<?php
namespace Engine\Location\Test\Integration\Model\City;

use Engine\Location\Model\City\CitiesByRegionList;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CitiesByRegionListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CitiesByRegionList
     */
    private $citiesByRegionList;

    protected function setUp()
    {
        parent::setUp();

        $this->citiesByRegionList = Bootstrap::getObjectManager()->get(CitiesByRegionList::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testGetList()
    {
        $result = $this->citiesByRegionList->getList(100);
        self::assertNotEmpty($result);

        $cities = $result->getItems();
        $totalCount = $result->getTotalCount();
        $searchCriteria = $result->getSearchCriteria();

        self::assertCount(2, $cities);
        self::assertEquals(2, $totalCount);
        self::assertInstanceOf(SearchCriteriaInterface::class, $searchCriteria);

        self::assertEquals(200, $cities[0]->getCityId());
        self::assertEquals('city-2', $cities[0]->getTitle());
        self::assertTrue($cities[0]->getIsEnabled());
        self::assertEquals(200, $cities[0]->getPosition());

        self::assertEquals(100, $cities[1]->getCityId());
        self::assertEquals('city-3', $cities[1]->getTitle());
        self::assertTrue($cities[1]->getIsEnabled());
        self::assertEquals(300, $cities[1]->getPosition());
    }
}
