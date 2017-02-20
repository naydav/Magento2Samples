<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category\Save;

use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Controller\Adminhtml\Category\Save;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Model\Category\Validator\UrlKeyValidator;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;
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
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testFailedValidationOnCreate($field, $value, $errorMessage)
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
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testFailedValidationOnUpdate($field, $value, $errorMessage)
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
    public function failedValidationDataProvider()
    {
        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $invalidMaxLengthUrlKey = str_repeat(1, UrlKeyValidator::MAX_URL_KEY_LENGTH + 1);
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
                $invalidMaxLengthUrlKey,
                'Value &quot;' . $invalidMaxLengthUrlKey . '&quot; for &quot;' . CategoryInterface::URL_KEY
                . '&quot; is more than ' . UrlKeyValidator::MAX_URL_KEY_LENGTH . ' characters long.',
            ],
            [
                CategoryInterface::URL_KEY,
                'Category-urlKey-200',
                'Category with such url &quot;Category-urlKey-200&quot; already exist (Category title: '
                    . 'Category-title-200, Category id: 200, Parent id: ' . $rootCategoryIdProvider->provide().  ').',
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
