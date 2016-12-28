<?php
namespace Engine\Location\Test\Integration\Backend;

use Engine\Backend\Test\AssertMenuItem;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MenuTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/backend';

    public function testMenu()
    {
        $this->dispatch(self::REQUEST_URI);
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        AssertMenuItem::assert($body, 'engine-location-location', 'Location');
        AssertMenuItem::assert($body, 'engine-location-region', 'Regions', 'location/region');
        AssertMenuItem::assert($body, 'engine-location-region-new', 'Add Region', 'location/region/new');
        AssertMenuItem::assert($body, 'engine-location-city', 'Cities', 'location/city');
        AssertMenuItem::assert($body, 'engine-location-city-new', 'Add City', 'location/city/new');
    }
}
