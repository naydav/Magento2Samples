<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Category\Api\Data\CategoryInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class NewActionTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/new';

    /**
     * @var string
     */
    private $formName = 'engine_category_form';

    public function testNew()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('New Category'));
        AssertPageHeader::assert($body, __('New Category'));
        AssertStoreSwitcher::assert($body, false);

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::PARENT_ID
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::URL_KEY
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::IS_ANCHOR
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::IS_ENABLED
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::POSITION
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::TITLE
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::DESCRIPTION
        );
    }
}
