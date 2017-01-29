<?php
namespace Engine\Location\Test\Integration\Model\Region\Source;

use Engine\Location\Model\Region\Source\RegionSource;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RegionSource
     */
    private $regionSource;

    protected function setUp()
    {
        parent::setUp();

        // use create instead get for prevent internal object caching
        $this->regionSource = Bootstrap::getObjectManager()->create(RegionSource::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope.php
     */
    public function testToOptionArray()
    {
        $options = $this->regionSource->toOptionArray();

        $expectedData = [
            [
                'value' => 400,
                'label' => 'Region-title-1',
            ],
            [
                'value' => 200,
                'label' => 'Region-title-2',
            ],
            [
                'value' => 300,
                'label' => 'Region-title-2',
            ],
            [
                'value' => 100,
                'label' => 'Region-title-3',
            ],
        ];
        self::assertEquals($expectedData, $options);
    }

    public function testToOptionArrayIfRegionsAreNotExist()
    {
        self::assertEmpty($this->regionSource->toOptionArray());
    }
}
