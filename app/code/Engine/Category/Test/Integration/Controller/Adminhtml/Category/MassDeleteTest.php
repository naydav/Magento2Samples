<?php
namespace Engine\Category\Test\Integration\Controller\Adminhtml\Category;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/massDelete';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->categoryRepository = $this->_objectManager->get(
            CategoryRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list.php
     */
    public function testMassDelete()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_category_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 3 Category(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(1, $this->getCategoriesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list.php
     */
    public function testMassDeleteWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_category_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertEquals(4, $this->getCategoriesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list.php
     */
    public function testMassDeleteWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                -1,
                400,
            ],
            'namespace' => 'engine_category_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 2 Category(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(2, $this->getCategoriesCount());
    }

    /**
     * @return int
     */
    private function getCategoriesCount()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CategoryInterface::PARENT_ID, $this->rootCategoryIdProvider->provide());
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->categoryRepository->getList($searchCriteria);
        return $result->getTotalCount();
    }
}
