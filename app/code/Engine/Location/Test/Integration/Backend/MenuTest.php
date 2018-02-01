<?php
namespace Engine\Location\Test\Integration\Backend;

use Engine\Test\Backend\AssertMenuItem;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author naydav <valeriy.nayda@gmail.com>
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

        AssertMenuItem::assert(
            $body,
            'engine-location-location',
            'Location'
        );

        AssertMenuItem::assert(
            $body,
            'engine-location-country-index',
            'Country List',
            'engine-location/country'
        );
        AssertMenuItem::assert(
            $body,
            'engine-location-country-new',
            'Add Country',
            'engine-location/country/new'
        );

        AssertMenuItem::assert(
            $body,
            'engine-location-region-index',
            'Region List',
            'engine-location/region'
        );
        AssertMenuItem::assert(
            $body,
            'engine-location-region-new',
            'Add Region',
            'engine-location/region/new'
        );

        AssertMenuItem::assert(
            $body,
            'engine-location-city-index',
            'City List',
            'engine-location/city'
        );
        AssertMenuItem::assert(
            $body,
            'engine-location-city-new',
            'Add City',
            'engine-location/city/new'
        );
    }
}
