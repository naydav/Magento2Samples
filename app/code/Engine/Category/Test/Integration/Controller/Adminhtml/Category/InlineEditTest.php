<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Category\Model\Category\Validator\UrlKeyValidator;
use Engine\Test\AssertArrayContains;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class InlineEditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/inlineEdit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->categoryRepository = $this->_objectManager->get(
            CategoryRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testInlineEditInGlobalScope()
    {
        $categoryId = 100;
        $itemData = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::URL_KEY => 'Category-urlKey-inline-edit',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title-inline-edit',
            CategoryInterface::DESCRIPTION => 'Category-description-inline-edit',
        ];

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                $itemData,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $category = $this->getCategoryById($categoryId, 'default');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($category));
        $category = $this->getCategoryById($categoryId, 'test_store');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($category));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $categoryId = 100;
        $itemDataForTestStore = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::URL_KEY => 'Category-urlKey-inline-edit',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title-inline-edit-per-store',
            CategoryInterface::DESCRIPTION => 'Category-description-inline-edit-per-store',
        ];

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                $itemDataForTestStore,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI . '/store/' . $storeCode . '/');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $category = $this->getCategoryById($categoryId, 'default');
        $itemDataForDefaultStore = array_merge($itemDataForTestStore, [
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ]);
        AssertArrayContains::assert($itemDataForDefaultStore, $this->hydrator->extract($category));

        $category = $this->getCategoryById($categoryId, $storeCode);
        AssertArrayContains::assert($itemDataForTestStore, $this->hydrator->extract($category));
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $categoryId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CategoryInterface::CATEGORY_ID => $categoryId,
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains(
            "[ID: {$categoryId}] The Category does not exist.",
            $jsonResponse->messages
        );
    }

    public function testInlineEditWithEmptyItems()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [],
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
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CategoryInterface::CATEGORY_ID => 100,
                    CategoryInterface::IS_ENABLED => false,
                ],
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
    public function testInlineEditWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CategoryInterface::CATEGORY_ID => 100,
                    CategoryInterface::IS_ENABLED => false,
                ],
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
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider validationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testValidation($field, $value, $errorMessage)
    {
        $categoryId = 100;
        $itemData = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::URL_KEY => 'Category-urlKey-inline-edit',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 1000,
            CategoryInterface::TITLE => 'Category-title-inline-edit',
            CategoryInterface::DESCRIPTION => 'Category-description-inline-edit',
        ];
        $itemData[$field] = $value;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                $itemData,
            ],
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
        $invalidMaxLengthUrlKey = str_repeat(1, UrlKeyValidator::MAX_URL_KEY_LENGTH + 1);
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
                . UrlKeyValidator::MAX_URL_KEY_LENGTH . ' characters long.',
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
     * @param int $categoryId
     * @param string|null $storeCode
     * @return CategoryInterface
     */
    private function getCategoryById($categoryId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $currentStore = $this->storeManager->getStore()->getCode();
            $this->storeManager->setCurrentStore($storeCode);
        }

        $category = $this->categoryRepository->get($categoryId);

        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($currentStore);
        }
        return $category;
    }
}
