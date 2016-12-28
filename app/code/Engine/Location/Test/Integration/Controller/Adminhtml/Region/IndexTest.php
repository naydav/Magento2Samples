<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Backend\Test\AssertAddButton;
use Engine\Backend\Test\AssertListing;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    public function testExecute()
    {
        $this->dispatch('backend/location/region');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, 'Manage Regions / Location');
        AssertPageHeader::assert($body, 'Manage Regions');
        AssertStoreSwitcher::assert($body);
        AssertAddButton::assert($body, 'add-button');
        AssertListing::assert($body, 'region_listing');
    }
}
