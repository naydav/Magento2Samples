<?php
namespace Engine\CategoryTree\Test\Integration\Backend;

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

        AssertMenuItem::assert(
            $body,
            'engine-category-category-category-tree',
            'Category Tree',
            'engine-category/category/tree'
        );
    }
}
