<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category\Save;

use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Controller\Adminhtml\Category\Save;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\CategoryBaseValidator;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class ValidationTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/save/store/%s';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->registry = $this->_objectManager->get(Registry::class);
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider validationDataProvider
     */
    public function testValidationOnCreate($field, $value, $errorMessage)
    {
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 100,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @dataProvider validationDataProvider
     */
    public function testValidationOnUpdate($field, $value, $errorMessage)
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 100,
            CategoryInterface::TITLE => 'Category-title-updated',
            CategoryInterface::DESCRIPTION => 'Category-description-updated',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            [
                CategoryInterface::PARENT_ID,
                null,
                'Category can&#039;t has empty parent.',
            ],
            [
                CategoryInterface::URL_KEY,
                '',
                '&quot;' . CategoryInterface::URL_KEY . '&quot; can not be empty.',
            ],
            [
                CategoryInterface::URL_KEY,
                str_repeat(1, 51),
                '&quot;' . CategoryInterface::URL_KEY . '&quot; is more than '
                . CategoryBaseValidator::MAX_URL_KEY_LENGTH . ' characters long.',
            ],
            [
                CategoryInterface::TITLE,
                '',
                '&quot;' . CategoryInterface::TITLE . '&quot; can not be empty.',
            ],
        ];
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testSetParentForRootCategory()
    {
        $data = [
            CategoryInterface::CATEGORY_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::PARENT_ID => 100,
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 100,
            CategoryInterface::TITLE => 'Category-title-updated',
            CategoryInterface::DESCRIPTION => 'Category-description-updated',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $errorMessage = 'Root Category can&#039;t has parent.';
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }
}
