<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Test\Backend\AssertAddButton;
use Engine\Test\Backend\AssertListing;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    public function testExecute()
    {
        $this->dispatch('backend/engine-location/region');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Manage Regions'));
        AssertPageHeader::assert($body, __('Manage Regions'));
        AssertAddButton::assert($body, 'add-button');
        AssertListing::assert($body, 'engine_location_region_listing');
    }
}
