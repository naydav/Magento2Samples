<?php
namespace Engine\CategoryTree\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertAddButton;
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
class TreeTest extends AbstractBackendController
{
    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testExecute()
    {
        $this->dispatch('backend/engine-category/category/tree');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, 'Categories Tree');
        AssertPageHeader::assert($body, 'Categories Tree');
        AssertStoreSwitcher::assert($body);
        AssertAddButton::assert($body, 'engine-category-buttons-add-button-button');
        \PHPUnit_Framework_Assert::assertSelectCount(
            '[data-ui-id=category-tree]',
            1,
            $body,
            'Tree container is missed'
        );
    }

    public function testExecuteWithEmptyTree()
    {
        $this->dispatch('backend/engine-category/category/tree');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        \PHPUnit_Framework_Assert::assertSelectCount(
            '[data-ui-id=tree-empty-header]',
            1,
            $body,
            'Tree empty header is missed'
        );
    }
}
