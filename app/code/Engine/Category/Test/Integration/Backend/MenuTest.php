<?php
namespace Engine\Category\Test\Integration\Backend;

use Engine\Backend\Test\AssertMenuItem;
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
            'engine-category-category',
            'Categories'
        );

        AssertMenuItem::assert(
            $body,
            'engine-category-category-index',
            'Category List',
            'engine-category/category'
        );
        AssertMenuItem::assert(
            $body,
            'engine-category-category-new',
            'Add Category',
            'engine-category/category/new'
        );
    }
}
