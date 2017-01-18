<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\CategoryUrlKeyValidator;
use Engine\Category\Api\Data\CategoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class ValidateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/validate/store/0';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;


    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider validationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testValidationOnCreate($field, $value, $errorMessage)
    {
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains($errorMessage, $jsonResponse->messages);
    }


    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider validationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testValidationOnUpdate($field, $value, $errorMessage)
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains($errorMessage, $jsonResponse->messages);
    }

    /**
     * @return array
     */
    public function validationDataProvider()
    {
        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = Bootstrap::getObjectManager()->get(RootCategoryIdProviderInterface::class);
        $invalidMaxLengthUrlKey = str_repeat(1, CategoryUrlKeyValidator::MAX_URL_KEY_LENGTH + 1);
        return [
            [
                CategoryInterface::URL_KEY,
                null,
                '"' . CategoryInterface::URL_KEY . '" can not be empty.',
            ],
            [
                CategoryInterface::URL_KEY,
                $invalidMaxLengthUrlKey,
                'Value "' . $invalidMaxLengthUrlKey . '" for "' . CategoryInterface::URL_KEY . '" is more than '
                . CategoryUrlKeyValidator::MAX_URL_KEY_LENGTH . ' characters long.',
            ],
            [
                CategoryInterface::URL_KEY,
                'Category-urlKey-200',
                'Category with such url "Category-urlKey-200" already exist (Category title: Category-title-200, '
                    . 'Category id: 200, Parent id: ' . $rootCategoryIdProvider->provide().  ').',
            ],
            [
                CategoryInterface::TITLE,
                null,
                '"' . CategoryInterface::TITLE . '" can not be empty.',
            ],
        ];
    }

    /**
     * @magentoApiDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testSetParentForRootCategory()
    {
        $data = [
            CategoryInterface::CATEGORY_ID => $this->rootCategoryIdProvider->provide(),
            CategoryInterface::PARENT_ID => 100,
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);

        $errorMessage = 'Root Category can\'t has parent.';
        self::assertContains($errorMessage, $jsonResponse->messages);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testValidateNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::CATEGORY_ID => 100,
                CategoryInterface::IS_ENABLED => false,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains('Please correct the data sent.', $jsonResponse->messages);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testValidateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::CATEGORY_ID => 100,
                CategoryInterface::IS_ENABLED => false,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains('Please correct the data sent.', $jsonResponse->messages);
    }
}
