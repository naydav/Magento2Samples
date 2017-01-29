<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertFormField;
use Engine\Backend\Test\AssertFormFieldNotPresent;
use Engine\Category\Api\RootCategoryIdProviderInterface;
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
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/edit';

    /**
     * @var string
     */
    private $formName = 'engine_category_form';

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    public function setUp()
    {
        parent::setUp();
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testEdit()
    {
        $categoryId = 100;
        $title = 'Category-title-100';

        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $categoryId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Category: %1', $title));
        AssertPageHeader::assert($body, __('Edit Category: %1', $title));
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::PARENT_ID,
            $this->rootCategoryIdProvider->provide()
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::URL_KEY,
            'Category-urlKey-100'
        );
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::IS_ANCHOR, true);
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::POSITION, 200);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::TITLE,
            $title
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::DESCRIPTION,
            'Category-description-100'
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $categoryId = 100;
        $title = 'Category-title-100-per-store';

        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $categoryId . '/store/' . $storeCode . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Category: %1', $title));
        AssertPageHeader::assert($body, __('Edit Category: %1', $title));
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::URL_KEY,
            'Category-urlKey-100'
        );
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::IS_ANCHOR, true);
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CategoryInterface::POSITION, 200);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::TITLE,
            $title
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::DESCRIPTION,
            'Category-description-100-per-store'
        );
    }

    public function testEditRootCategory()
    {
        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $this->rootCategoryIdProvider->provide() . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertFormFieldNotPresent::assert(
            $body,
            $this->formName,
            'general',
            CategoryInterface::PARENT_ID
        );
    }

    public function testEditWithNotExistEntityId()
    {
        $categoryId = -1;

        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $categoryId . '/'
        );

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages(
            $this->contains('Category with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
