<?php
namespace Engine\CategoryCharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup\Save;

use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
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
class CharacteristicGroupRelationsManagementTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/save/store/%s/back/edit';

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
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->categoryRepository = $this->_objectManager->get(CategoryRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list.php
     */
    public function testCreateWithCategoriesAssignment()
    {
        $data = [
            CharacteristicGroupInterface::TITLE => 'characteristic-group-title',
        ];
        $assignedCategories = [
            [
                RelationInterface::CATEGORY_ID => 100,
            ],
            [
                RelationInterface::CATEGORY_ID => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'categories' => [
                'assigned_categories' => $assignedCategories,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupByTitle($data[CategoryInterface::TITLE]);
        self::assertNotEmpty($characteristicGroup);

        $categories = $this->getAssignedCategories($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $categories);
        self::assertEquals(100, $categories[0]->getCategoryId());
        self::assertEquals(200, $categories[1]->getCategoryId());

        $characteristicGroupId = $characteristicGroup->getCharacteristicGroupId();
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId
            )
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Category/Test/_files/category/category_list.php
     */
    public function testUpdateWithCategoriesAssignment()
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCategories = [
            [
                RelationInterface::CATEGORY_ID => 100,
            ],
            [
                RelationInterface::CATEGORY_ID => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'categories' => [
                'assigned_categories' => $assignedCategories,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/' . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $categories = $this->getAssignedCategories($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $categories);
        self::assertEquals(100, $categories[0]->getCategoryId());
        self::assertEquals(200, $categories[1]->getCategoryId());
    }

    /**
     * Unassign Characteristic-Group-2 (id:200) from Category-3 (id:300)
     *
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUnassignCategoryFromCharacteristicGroup()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCategories = [
            [
                RelationInterface::CATEGORY_ID => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'categories' => [
                'assigned_categories' => $assignedCategories,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/' . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $categories = $this->getAssignedCategories($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(1, $categories);
        self::assertEquals(200, $categories[0]->getCategoryId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUnassignAllCategoriesFromCharacteristicGroup()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCategories = [];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'categories' => [
                'assigned_categories' => $assignedCategories,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/' . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $categories = $this->getAssignedCategories($characteristicGroup->getCharacteristicGroupId());
        self::assertEmpty($categories);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCategories = [
            [
                RelationInterface::CATEGORY_ID => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'categories' => [
                'assigned_categories' => $assignedCategories,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $categories = $this->getAssignedCategories($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $categories);
        self::assertEquals(200, $categories[0]->getCategoryId());
        self::assertEquals(300, $categories[1]->getCategoryId());
    }

    /**
     * @param string $title
     * @return CharacteristicGroupInterface
     */
    private function getCharacteristicGroupByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(CharacteristicGroupInterface::TITLE, $title)
            ->create();

        $result = $this->characteristicGroupRepository->getList($searchCriteria);
        $items = $result->getItems();
        $category = reset($items);
        return $category;
    }

    /**
     * @param int $characteristicGroupId
     * @return CharacteristicGroupInterface
     */
    private function getCharacteristicGroupById($characteristicGroupId)
    {
        $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);
        return $characteristicGroup;
    }

    /**
     * @param int $characteristicGroupId
     * @return CategoryInterface[]
     */
    private function getAssignedCategories($characteristicGroupId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic_group', $characteristicGroupId)
            ->create();

        $items = $this->categoryRepository->getList($searchCriteria)->getItems();
        return $items;
    }
}
