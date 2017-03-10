<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup\Save;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/save/store/%s/back/edit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

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
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-updated',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-updated',
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
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $characteristicGroupId,
            $this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY)
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'default');
        AssertArrayContains::assert($data, $this->hydrator->extract($characteristicGroup));
        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'test_store');
        AssertArrayContains::assert($data, $this->hydrator->extract($characteristicGroup));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $characteristicGroupId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-per-store',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-per-store',
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
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $characteristicGroupId,
            $this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY)
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $this->hydrator->extract($characteristicGroup));

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $this->hydrator->extract($characteristicGroup));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $characteristicGroupId = 100;
        $storeCode = 'test_store';

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
                CharacteristicGroupInterface::IS_ENABLED => false,
                CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
                CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-per-store',
                CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-per-store',
                '_use_default' => [
                    CharacteristicGroupInterface::TITLE => 1,
                    CharacteristicGroupInterface::DESCRIPTION => 1,
                ],
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, $storeCode));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
                . $characteristicGroupId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        $expectedData = [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ];
        AssertArrayContains::assert($expectedData, $this->hydrator->extract($characteristicGroup));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                CharacteristicGroupInterface::IS_ENABLED => false,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => -1,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group does not exist.'),
            MessageInterface::TYPE_ERROR
        );
        self::assertNull($this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY));
    }

    /**
     * @param int $characteristicGroupId
     * @param string|null $storeCode
     * @return CharacteristicGroupInterface
     */
    private function getCharacteristicGroupById($characteristicGroupId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($storeCode);
        }

        $characteristicGroup = $this->characteristicGroupRepository->get($characteristicGroupId);
        return $characteristicGroup;
    }
}
