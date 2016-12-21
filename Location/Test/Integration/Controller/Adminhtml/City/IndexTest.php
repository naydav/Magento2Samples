<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Backend\Test\AssertAddButton;
use Engine\Backend\Test\AssertListing;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    public function testExecute()
    {
        $this->dispatch('backend/location/city/index');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        AssertPageTitle::assert($body, 'Manage Cities / Location');
        AssertPageHeader::assert($body, 'Manage Cities');
        AssertStoreSwitcher::assert($body);
        AssertAddButton::assert($body);
        AssertListing::assert($body, 'city_listing');
    }
}
