<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\Test\AssertArrayContains;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
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
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/inlineEdit';

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

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testInlineEditInGlobalScope()
    {
        $characteristicGroupId = 100;
        $itemData = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-inline-edit',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-inline-edit',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-inline-edit',
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

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'default');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($characteristicGroup));
        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'test_store');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($characteristicGroup));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100_store_scope.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $characteristicGroupId = 100;
        $itemDataForTestStore = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-inline-edit',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-inline-edit-per-store',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-inline-edit-per-store',
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

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, 'default');
        $itemDataForDefaultStore = array_merge($itemDataForTestStore, [
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-100',
        ]);
        AssertArrayContains::assert($itemDataForDefaultStore, $this->hydrator->extract($characteristicGroup));

        $characteristicGroup = $this->getCharacteristicGroupById($characteristicGroupId, $storeCode);
        AssertArrayContains::assert($itemDataForTestStore, $this->hydrator->extract($characteristicGroup));
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $characteristicGroupId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
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
            "[ID: {$characteristicGroupId}] The Characteristic Group does not exist.",
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
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                    CharacteristicGroupInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
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
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                    CharacteristicGroupInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidation($field, $value, $errorMessage)
    {
        $characteristicGroupId = 100;
        $itemData = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-inline-edit',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-inline-edit',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-inline-edit',
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
    public function failedValidationDataProvider()
    {
        return [
            [
                CharacteristicGroupInterface::TITLE,
                '',
                '"' . CharacteristicGroupInterface::TITLE . '" can not be empty.',
            ],
        ];
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
