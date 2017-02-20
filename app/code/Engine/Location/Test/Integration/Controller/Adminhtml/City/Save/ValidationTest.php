<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City\Save;

use Engine\Location\Controller\Adminhtml\City\Save;
use Engine\Location\Api\Data\CityInterface;
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
    const REQUEST_URI = 'backend/engine-location/city/save/store/%s';

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
            CityInterface::REGION_ID => 100,
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 100,
            CityInterface::TITLE => 'City-title',
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
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CITY_ID_KEY));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     */
    public function testFailedValidationOnUpdate($field, $value, $errorMessage)
    {
        $cityId = 100;
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 100,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 200,
            CityInterface::TITLE => 'City-title-updated',
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
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CITY_ID_KEY));
    }

    /**
     * @return array
     */
    public function failedValidationDataProvider()
    {
        return [
            [
                CityInterface::TITLE,
                '',
                '&quot;' . CityInterface::TITLE . '&quot; can not be empty.',
            ],
        ];
    }
}
