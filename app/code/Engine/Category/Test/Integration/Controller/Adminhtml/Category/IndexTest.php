<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertAddButton;
use Engine\Backend\Test\AssertListing;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
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
        $this->dispatch('backend/engine-category/category');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Manage Categories'));
        AssertPageHeader::assert($body, __('Manage Categories'));
        AssertStoreSwitcher::assert($body);
        AssertAddButton::assert($body, 'add-button');
        AssertListing::assert($body, 'engine_category_listing');
    }
}
