<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category\Save;

use Engine\Category\Controller\Adminhtml\Category\Save;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
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
class CreateTest extends AbstractBackendController
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
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

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
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->categoryRepository = $this->_objectManager->get(
            CategoryRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->registry = $this->_objectManager->get(Registry::class);
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
    }

    public function testCreate()
    {
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->get(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::IS_ANCHOR => true,
            CategoryInterface::IS_ENABLED => true,
            CategoryInterface::POSITION => 100,
            CategoryInterface::TITLE => 'Category-title',
            CategoryInterface::DESCRIPTION => 'Category-description',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0) . '/back/edit');

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $category = $this->getCategoryByTitle($data[CategoryInterface::TITLE]);
        self::assertNotEmpty($category);
        AssertArrayContains::assert($data, $this->hydrator->extract($category));

        $redirect = 'backend/engine-category/category/edit/category_id/'
            . $category->getCategoryId();
        $this->assertRedirect($this->stringContains($redirect));

        self::assertEquals(
            $category->getCategoryId(),
            $this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY)
        );
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->get(),
                CategoryInterface::URL_KEY => 'Category-urlKey',
                CategoryInterface::IS_ANCHOR => true,
                CategoryInterface::IS_ENABLED => true,
                CategoryInterface::POSITION => 200,
                CategoryInterface::TITLE => 'Category-title',
                CategoryInterface::DESCRIPTION => 'Category-description',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category/new'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    public function testCreateAndClose()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->get(),
                CategoryInterface::URL_KEY => 'Category-urlKey',
                CategoryInterface::IS_ANCHOR => true,
                CategoryInterface::IS_ENABLED => true,
                CategoryInterface::POSITION => 200,
                CategoryInterface::TITLE => 'Category-title',
                CategoryInterface::DESCRIPTION => 'Category-description',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertRedirect(
            $this->matchesRegularExpression(
                '~^((?!' . CategoryInterface::CATEGORY_ID . '|new).)*$~'
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Category has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    public function testCreateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->get(),
                CategoryInterface::URL_KEY => 'Category-urlKey',
                CategoryInterface::IS_ANCHOR => true,
                CategoryInterface::IS_ENABLED => true,
                CategoryInterface::POSITION => 200,
                CategoryInterface::TITLE => 'Category-title',
                CategoryInterface::DESCRIPTION => 'Category-description',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CATEGORY_ID_KEY));
    }

    /**
     * @param string $title
     * @return CategoryInterface
     */
    private function getCategoryByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CategoryInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->categoryRepository->getList($searchCriteria);
        $items = $result->getItems();
        $category = reset($items);
        return $category;
    }
}
