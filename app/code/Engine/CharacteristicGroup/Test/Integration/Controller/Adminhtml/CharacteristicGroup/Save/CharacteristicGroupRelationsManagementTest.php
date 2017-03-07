<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup\Save;

use Engine\Characteristic\Api\CharacteristicRepositoryInterface;
use Engine\Characteristic\Api\Data\CharacteristicInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
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
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

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
        $this->characteristicRepository = $this->_objectManager->get(CharacteristicRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_list.php
     */
    public function testCreateWithCharacteristicsAssignment()
    {
        $data = [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
        ];
        $assignedCharacteristics = [
            [
                RelationInterface::CHARACTERISTIC_ID => 100,
                RelationInterface::CHARACTERISTIC_POSITION => 2,
            ],
            [
                RelationInterface::CHARACTERISTIC_ID => 200,
                RelationInterface::CHARACTERISTIC_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristics' => [
                'assigned_characteristics' => $assignedCharacteristics,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupByTitle($data[CharacteristicGroupInterface::TITLE]);
        self::assertNotEmpty($characteristicGroup);

        $characteristics = $this->getAssignedCharacteristics($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $characteristics);
        self::assertEquals(200, $characteristics[0]->getCharacteristicId());
        self::assertEquals(100, $characteristics[1]->getCharacteristicId());

        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroup->getCharacteristicGroupId()
            )
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_list.php
     */
    public function testUpdateWithCharacteristicsAssignment()
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCharacteristics = [
            [
                RelationInterface::CHARACTERISTIC_ID => 100,
                RelationInterface::CHARACTERISTIC_POSITION => 2,
            ],
            [
                RelationInterface::CHARACTERISTIC_ID => 200,
                RelationInterface::CHARACTERISTIC_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristics' => [
                'assigned_characteristics' => $assignedCharacteristics,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $characteristics = $this->getAssignedCharacteristics($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $characteristics);
        self::assertEquals(200, $characteristics[0]->getCharacteristicId());
        self::assertEquals(100, $characteristics[1]->getCharacteristicId());
    }

    /**
     * Unassign Characteristic-1 (id:100, position:2) from Characteristic-Group-2 (id:200)
     *
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUnassignCharacteristicFromCharacteristicGroup()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCharacteristics = [
            [
                RelationInterface::CHARACTERISTIC_ID => 200,
                RelationInterface::CHARACTERISTIC_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristics' => [
                'assigned_characteristics' => $assignedCharacteristics,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $characteristics = $this->getAssignedCharacteristics($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(1, $characteristics);
        self::assertEquals(200, $characteristics[0]->getCharacteristicId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUnassignAllCharacteristicsFromCharacteristicGroup()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCharacteristics = [];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristics' => [
                'assigned_characteristics' => $assignedCharacteristics,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $characteristics = $this->getAssignedCharacteristics($characteristicGroup->getCharacteristicGroupId());
        self::assertEmpty($characteristics);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $characteristicGroupId = 200;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
        ];
        $assignedCharacteristics = [
            [
                RelationInterface::CHARACTERISTIC_ID => 200,
                RelationInterface::CHARACTERISTIC_POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'characteristics' => [
                'assigned_characteristics' => $assignedCharacteristics,
            ],
        ]);

        $this->dispatch(sprintf(self::REQUEST_URI, 0));
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId);
        self::assertNotEmpty($characteristicGroup);

        $characteristics = $this->getAssignedCharacteristics($characteristicGroup->getCharacteristicGroupId());
        self::assertCount(2, $characteristics);
        self::assertEquals(200, $characteristics[0]->getCharacteristicId());
        self::assertEquals(100, $characteristics[1]->getCharacteristicId());
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
        $characteristicGroup = reset($items);
        return $characteristicGroup;
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
     * @return CharacteristicInterface[]
     */
    private function getAssignedCharacteristics($characteristicGroupId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic_group', $characteristicGroupId)
            ->create();

        $items = $this->characteristicRepository->getList($searchCriteria)->getItems();
        return $items;
    }
}
