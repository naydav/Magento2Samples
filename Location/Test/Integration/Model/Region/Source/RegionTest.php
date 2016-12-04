<?php
namespace Engine\Location\Test\Integration\Model\Region\Source;

use Engine\Location\Model\Region\Source\Region;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class CitiesByRegionListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Region
     */
    private $sourceRegion;

    protected function setUp()
    {
        parent::setUp();

        // use create instead get for prevent internal object caching
        $this->sourceRegion = Bootstrap::getObjectManager()->create(Region::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope_data.php
     */
    public function testToOptionArray()
    {
        $options = $this->sourceRegion->toOptionArray();

        $expectedData = [
            [
                'value' => 100,
                'label' => 'region-3',
            ],
            [
                'value' => 200,
                'label' => 'region-2',
            ],
            [
                'value' => 300,
                'label' => 'region-2',
            ],
            [
                'value' => 400,
                'label' => 'region-1',
            ],
        ];
        self::assertEquals($expectedData, $options);
    }

    public function testToOptionArrayIfRegionsAreNotExist()
    {
        self::assertEquals([], $this->sourceRegion->toOptionArray());
    }
}
