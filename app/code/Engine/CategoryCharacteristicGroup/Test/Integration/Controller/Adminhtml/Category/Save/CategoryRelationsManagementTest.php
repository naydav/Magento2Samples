<?php
namespace Engine\CategoryCharacteristicGroup\Test\Integration\Controller\Adminhtml\Category\Save;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
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
 * @magentoDbIsolation enabled
 */
class CategoryRelationsManagementTest extends AbstractBackendController
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
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
        $this->categoryRepository = $this->_objectManager->get(CategoryRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list.php
     */
    public function testCreateWithGroupsAssignment()
    {
        $data = [
            CategoryInterface::PARENT_ID => $this->rootCategoryIdProvider->get(),
            CategoryInterface::URL_KEY => 'Category-urlKey',
            CategoryInterface::TITLE => 'Category-title',
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 2,
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristic_groups' => [
                'assigned_characteristic_groups' => $assignedCharacteristicGroups,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Category has been saved.'), MessageInterface::TYPE_SUCCESS);

        $category = $this->getCategoryByTitle($data[CategoryInterface::TITLE]);
        self::assertNotEmpty($category);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($category->getCategoryId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(100, $characteristicGroups[1]->getCharacteristicGroupId());

        $this->assertRedirect(
            $this->stringContains('backend/engine-category/category/edit/category_id/' . $category->getCategoryId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list.php
     */
    public function testUpdateWithCharacteristicGroupsAssignment()
    {
        $categoryId = 100;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 2,
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristic_groups' => [
                'assigned_characteristic_groups' => $assignedCharacteristicGroups,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains('backend/engine-category/category/edit/category_id/' . $categoryId)
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Category has been saved.'), MessageInterface::TYPE_SUCCESS);

        $category = $this->getCategoryById($categoryId);
        self::assertNotEmpty($category);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($category->getCategoryId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(100, $characteristicGroups[1]->getCharacteristicGroupId());
    }

    /**
     * Unassign Characteristic-Group-1 (id:100, position:2) from Category-2 (id:200)
     *
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUnassignCharacteristicGroupFromCategory()
    {
        $categoryId = 200;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristic_groups' => [
                'assigned_characteristic_groups' => $assignedCharacteristicGroups,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category/edit/category_id/' . $categoryId));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Category has been saved.'), MessageInterface::TYPE_SUCCESS);

        $category = $this->getCategoryById($categoryId);
        self::assertNotEmpty($category);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($category->getCategoryId());
        self::assertCount(1, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUnassignAllCharacteristicGroupsFromCategory()
    {
        $categoryId = 200;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
        ];
        $assignedCharacteristicGroups = [];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristic_groups' => [
                'assigned_characteristic_groups' => $assignedCharacteristicGroups,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category/edit/category_id/' . $categoryId));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Category has been saved.'), MessageInterface::TYPE_SUCCESS);

        $category = $this->getCategoryById($categoryId);
        self::assertNotEmpty($category);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($category->getCategoryId());
        self::assertEmpty($characteristicGroups);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $categoryId = 200;
        $data = [
            CategoryInterface::CATEGORY_ID => $categoryId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristic_groups' => [
                'assigned_characteristic_groups' => $assignedCharacteristicGroups,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-category/category'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);

        $category = $this->getCategoryById($categoryId);
        self::assertNotEmpty($category);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($category->getCategoryId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(100, $characteristicGroups[1]->getCharacteristicGroupId());
    }

    /**
     * @param string $title
     * @return CategoryInterface
     */
    private function getCategoryByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(CategoryInterface::TITLE, $title)
            ->create();

        $result = $this->categoryRepository->getList($searchCriteria);
        $items = $result->getItems();
        $category = reset($items);
        return $category;
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface
     */
    private function getCategoryById($categoryId)
    {
        $category = $this->categoryRepository->get($categoryId);
        return $category;
    }

    /**
     * @param int $categoryId
     * @return CharacteristicGroupInterface[]
     */
    private function getAssignedCharacteristicGroups($categoryId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_category', $categoryId)
            ->create();

        $items = $this->characteristicGroupRepository->getList($searchCriteria)->getItems();
        return $items;
    }
}
