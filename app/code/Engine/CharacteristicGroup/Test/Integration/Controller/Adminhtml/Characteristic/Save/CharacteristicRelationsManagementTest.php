<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\Characteristic\Save;

use Engine\Characteristic\Api\Data\CharacteristicInterface;
use Engine\Characteristic\Model\Characteristic\Source\TypeSource;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\Characteristic\Api\CharacteristicRepositoryInterface;
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
class CharacteristicRelationsManagementTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic/characteristic/save/store/%s/back/edit';

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
     * @var CharacteristicRepositoryInterface
     */
    private $characteristicRepository;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->characteristicRepository = $this->_objectManager->get(CharacteristicRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list.php
     */
    public function testCreateWithCharacteristicGroupsAssignment()
    {
        $data = [
            CharacteristicInterface::TITLE => 'Characteristic-title',
            CharacteristicInterface::TYPE => TypeSource::TYPE_INTEGER,
            CharacteristicInterface::CODE => 'characteristic_code_updated',
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
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
        $this->assertSessionMessages(
            $this->contains('The Characteristic has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristic = $this->getCharacteristicByTitle($data[CharacteristicInterface::TITLE]);
        self::assertNotEmpty($characteristic);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($characteristic->getCharacteristicId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(100, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(200, $characteristicGroups[1]->getCharacteristicGroupId());

        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic/characteristic/edit/characteristic_id/'
                . $characteristic->getCharacteristicId()
            )
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Characteristic/Test/_files/characteristic/characteristic_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list.php
     */
    public function testUpdateWithCharacteristicGroupsAssignment()
    {
        $characteristicId = 100;
        $data = [
            CharacteristicInterface::CHARACTERISTIC_ID => $characteristicId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 100,
            ],
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
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
            $this->stringContains(
                'backend/engine-characteristic/characteristic/edit/characteristic_id/'
                . $characteristicId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristic = $this->getCharacteristicById($characteristicId);
        self::assertNotEmpty($characteristic);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($characteristic->getCharacteristicId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(100, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(200, $characteristicGroups[1]->getCharacteristicGroupId());
    }

    /**
     * Unassign Characteristic-2 (id:200) from Characteristic-Group-3 (id:300)
     *
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUnassignCharacteristicGroupFromCharacteristic()
    {
        $characteristicId = 200;
        $data = [
            CharacteristicInterface::CHARACTERISTIC_ID => $characteristicId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
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
            $this->stringContains(
                'backend/engine-characteristic/characteristic/edit/characteristic_id/'
                . $characteristicId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristic = $this->getCharacteristicById($characteristicId);
        self::assertNotEmpty($characteristic);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($characteristic->getCharacteristicId());
        self::assertCount(1, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUnassignAllCharacteristicGroupsFromCharacteristic()
    {
        $characteristicId = 200;
        $data = [
            CharacteristicInterface::CHARACTERISTIC_ID => $characteristicId,
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
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic/characteristic/edit/characteristic_id/'
                . $characteristicId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristic = $this->getCharacteristicById($characteristicId);
        self::assertNotEmpty($characteristic);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($characteristic->getCharacteristicId());
        self::assertEmpty($characteristicGroups);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $characteristicId = 200;
        $data = [
            CharacteristicInterface::CHARACTERISTIC_ID => $characteristicId,
        ];
        $assignedCharacteristicGroups = [
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID => 200,
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
        $this->assertRedirect($this->stringContains('backend/engine-characteristic/characteristic'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);

        $characteristic = $this->getCharacteristicById($characteristicId);
        self::assertNotEmpty($characteristic);

        $characteristicGroups = $this->getAssignedCharacteristicGroups($characteristic->getCharacteristicId());
        self::assertCount(2, $characteristicGroups);
        self::assertEquals(200, $characteristicGroups[0]->getCharacteristicGroupId());
        self::assertEquals(300, $characteristicGroups[1]->getCharacteristicGroupId());
    }

    /**
     * @param string $title
     * @return CharacteristicInterface
     */
    private function getCharacteristicByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter(CharacteristicInterface::TITLE, $title)
            ->create();

        $result = $this->characteristicRepository->getList($searchCriteria);
        $items = $result->getItems();
        $characteristic = reset($items);
        return $characteristic;
    }

    /**
     * @param int $characteristicId
     * @return CharacteristicInterface
     */
    private function getCharacteristicById($characteristicId)
    {
        $characteristic = $this->characteristicRepository->get($characteristicId);
        return $characteristic;
    }

    /**
     * @param int $characteristicId
     * @return CharacteristicGroupInterface[]
     */
    private function getAssignedCharacteristicGroups($characteristicId)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('assigned_to_characteristic', $characteristicId)
            ->create();

        $items = $this->characteristicGroupRepository->getList($searchCriteria)->getItems();
        return $items;
    }
}
