<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Category\Controller\Adminhtml\Category\Save;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class UpdateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/save/store/%s/back/edit';

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

    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->categoryRepository = $this->_objectManager->get(
            CategoryRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => 200,
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
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-category/category/edit/category_id/'
                . $categoryId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $categoryId,
            $this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY)
        );

        $category = $this->getCategoryById($categoryId, 'default');
        AssertArrayContains::assert($data, $this->hydrator->extract($category));
        $category = $this->getCategoryById($categoryId, 'test_store');
        AssertArrayContains::assert($data, $this->hydrator->extract($category));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $categoryId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            CategoryInterface::CATEGORY_ID => $categoryId,
            CategoryInterface::PARENT_ID => 200,
            CategoryInterface::URL_KEY => 'Category-urlKey-updated',
            CategoryInterface::IS_ANCHOR => false,
            CategoryInterface::IS_ENABLED => false,
            CategoryInterface::POSITION => 100,
            CategoryInterface::TITLE => 'Category-title-per-store',
            CategoryInterface::DESCRIPTION => 'Category-description-per-store',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $dataForTestStore,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, $storeCode));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-category/category/edit/category_id/'
                . $categoryId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $categoryId,
            $this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY)
        );

        $category = $this->getCategoryById($categoryId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $this->hydrator->extract($category));

        $category = $this->getCategoryById($categoryId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $this->hydrator->extract($category));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100_store_scope.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_200.php
     */
    public function testDeleteValueInStoreScope()
    {
        $categoryId = 100;
        $storeCode = 'test_store';

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::CATEGORY_ID => $categoryId,
                CategoryInterface::PARENT_ID => 200,
                CategoryInterface::URL_KEY => 'Category-urlKey-updated',
                CategoryInterface::IS_ANCHOR => false,
                CategoryInterface::IS_ENABLED => false,
                CategoryInterface::POSITION => 100,
                CategoryInterface::TITLE => 'Category-title-per-store',
                CategoryInterface::DESCRIPTION => 'Category-description-per-store',
                '_use_default' => [
                    CategoryInterface::TITLE => 1,
                    CategoryInterface::DESCRIPTION => 1,
                ],
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, $storeCode));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-category/category/edit/category_id/'
                . $categoryId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $category = $this->getCategoryById($categoryId, $storeCode);
        $expectedData = [
            CategoryInterface::TITLE => 'Category-title-100',
            CategoryInterface::DESCRIPTION => 'Category-description-100',
        ];
        AssertArrayContains::assert($expectedData, $this->hydrator->extract($category));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::CATEGORY_ID => 100,
                CategoryInterface::TITLE => 'Category-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::CATEGORY_ID => -1,
                CategoryInterface::TITLE => 'Category-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages(
            $this->contains('The Category does not exist.'),
            MessageInterface::TYPE_ERROR
        );
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }

    /**
     * @param int $categoryId
     * @param string|null $storeCode
     * @return CategoryInterface
     */
    private function getCategoryById($categoryId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($storeCode);
        }

        $category = $this->categoryRepository->get($categoryId);
        return $category;
    }
}
