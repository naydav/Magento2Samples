<?php
namespace Engine\CategoryTree\Test\Integration\Controller\Adminhtml\Category;

use Engine\Test\AssertArrayEquals;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\CategoryTree\Api\CategoryTreeLoaderInterface;
use Engine\CategoryTree\Api\Data\CategoryTreeInterface;
use Engine\Tree\Api\Data\MoveDataInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MoveTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/move';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var CategoryTreeLoaderInterface
     */
    private $categoryTreeLoader;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CategoryInterface
     */
    private $rootCategory;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->categoryTreeLoader = $this->_objectManager->get(CategoryTreeLoaderInterface::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);

        /** @var RootCategoryIdProviderInterface $rootCategoryIdProvider */
        $rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
        /** @var CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->_objectManager->get(CategoryRepositoryInterface::class);
        $this->rootCategory = $categoryRepository->get($rootCategoryIdProvider->provide());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testMove()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 200,
                MoveDataInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                MoveDataInterface::AFTER_ID => 400,
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

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 0,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 200,
                    CategoryTreeInterface::TITLE => 'Category-title-1-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 200,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 1,
                        CategoryInterface::TITLE => 'Category-title-1-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 2,
                        CategoryInterface::TITLE => 'Category-title-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 300,
                            CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 300,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 200,
                                CategoryInterface::TITLE => 'Category-title-1-2',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->categoryTreeLoader->getTree();
        $actualData = $this->hydrator->extract($tree);
        AssertArrayEquals::assert($expectedData, $actualData, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * If afterId is missed then move on first position
     *
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testChangeParentWithoutAfterId()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 100,
                MoveDataInterface::PARENT_ID => 400,
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

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 100,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 100,
                            CategoryTreeInterface::TITLE => 'Category-title-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 100,
                                CategoryInterface::PARENT_ID => 400,
                                CategoryInterface::POSITION => 0,
                                CategoryInterface::TITLE => 'Category-title-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [
                                [
                                    CategoryTreeInterface::ID => 200,
                                    CategoryTreeInterface::TITLE => 'Category-title-1-1',
                                    CategoryTreeInterface::CATEGORY => [
                                        CategoryInterface::CATEGORY_ID => 200,
                                        CategoryInterface::PARENT_ID => 100,
                                        CategoryInterface::POSITION => 100,
                                        CategoryInterface::TITLE => 'Category-title-1-1',
                                    ],
                                    CategoryTreeInterface::CHILDREN => [],
                                ],
                                [
                                    CategoryTreeInterface::ID => 300,
                                    CategoryTreeInterface::TITLE => 'Category-title-1-2',
                                    CategoryTreeInterface::CATEGORY => [
                                        CategoryInterface::CATEGORY_ID => 300,
                                        CategoryInterface::PARENT_ID => 100,
                                        CategoryInterface::POSITION => 200,
                                        CategoryInterface::TITLE => 'Category-title-1-2',
                                    ],
                                    CategoryTreeInterface::CHILDREN => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->categoryTreeLoader->getTree();
        $actualData = $this->hydrator->extract($tree);
        AssertArrayEquals::assert($expectedData, $actualData, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testChangePositionWithoutChangingParent()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 300,
                MoveDataInterface::PARENT_ID => 100,
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

        $expectedData = [
            CategoryTreeInterface::ID => $this->rootCategory->getCategoryId(),
            CategoryTreeInterface::TITLE => $this->rootCategory->getTitle(),
            CategoryTreeInterface::CATEGORY => [
                CategoryInterface::CATEGORY_ID => $this->rootCategory->getCategoryId(),
                CategoryInterface::POSITION => $this->rootCategory->getPosition(),
                CategoryInterface::TITLE => $this->rootCategory->getTitle(),
            ],
            CategoryTreeInterface::CHILDREN => [
                [
                    CategoryTreeInterface::ID => 400,
                    CategoryTreeInterface::TITLE => 'Category-title-2',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 400,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 100,
                        CategoryInterface::TITLE => 'Category-title-2',
                    ],
                    CategoryTreeInterface::CHILDREN => [],
                ],
                [
                    CategoryTreeInterface::ID => 100,
                    CategoryTreeInterface::TITLE => 'Category-title-1',
                    CategoryTreeInterface::CATEGORY => [
                        CategoryInterface::CATEGORY_ID => 100,
                        CategoryInterface::PARENT_ID => $this->rootCategory->getCategoryId(),
                        CategoryInterface::POSITION => 200,
                        CategoryInterface::TITLE => 'Category-title-1',
                    ],
                    CategoryTreeInterface::CHILDREN => [
                        [
                            CategoryTreeInterface::ID => 300,
                            CategoryTreeInterface::TITLE => 'Category-title-1-2',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 300,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 0,
                                CategoryInterface::TITLE => 'Category-title-1-2',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                        [
                            CategoryTreeInterface::ID => 200,
                            CategoryTreeInterface::TITLE => 'Category-title-1-1',
                            CategoryTreeInterface::CATEGORY => [
                                CategoryInterface::CATEGORY_ID => 200,
                                CategoryInterface::PARENT_ID => 100,
                                CategoryInterface::POSITION => 1,
                                CategoryInterface::TITLE => 'Category-title-1-1',
                            ],
                            CategoryTreeInterface::CHILDREN => [],
                        ],
                    ],
                ],
            ],
        ];
        $tree = $this->categoryTreeLoader->getTree();
        $actualData = $this->hydrator->extract($tree);
        AssertArrayEquals::assert($expectedData, $actualData, [
            CategoryInterface::URL_KEY,
            CategoryInterface::IS_ANCHOR,
            CategoryInterface::IS_ENABLED,
            CategoryInterface::DESCRIPTION,
            ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY,
        ]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testMoveWithEmptyParent()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 300,
                MoveDataInterface::PARENT_ID => '',
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
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testMoveNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 300,
                MoveDataInterface::PARENT_ID => 100,
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
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_tree.php
     */
    public function testMoveWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'moveData' => [
                MoveDataInterface::ID => 300,
                MoveDataInterface::PARENT_ID => 100,
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
