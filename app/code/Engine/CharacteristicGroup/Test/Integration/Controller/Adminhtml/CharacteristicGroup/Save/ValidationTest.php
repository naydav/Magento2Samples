<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup\Save;

use Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup\Save;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Framework\Data\Form\FormKey;
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
class ValidationTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/save/store/%s';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidationOnCreate($field, $value, $errorMessage)
    {
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_id_100.php
     */
    public function testFailedValidationOnUpdate($field, $value, $errorMessage)
    {
        $characteristicGroupId = 100;
        $data = [
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => $characteristicGroupId,
            CharacteristicGroupInterface::IS_ENABLED => false,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-updated',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-updated',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description-updated',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY));
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
                '&quot;' . CharacteristicGroupInterface::TITLE . '&quot; can not be empty.',
            ],
        ];
    }
}
