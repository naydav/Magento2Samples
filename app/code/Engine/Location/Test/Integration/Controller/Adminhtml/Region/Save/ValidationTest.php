<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

use Engine\Location\Controller\Adminhtml\Region\Save;
use Engine\Location\Api\Data\RegionInterface;
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
    const REQUEST_URI = 'backend/engine-location/region/save/store/%s';

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
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'Region-title',
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
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_REGION_ID_KEY));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testFailedValidationOnUpdate($field, $value, $errorMessage)
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 200,
            RegionInterface::TITLE => 'Region-title-updated',
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
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_REGION_ID_KEY));
    }

    /**
     * @return array
     */
    public function failedValidationDataProvider()
    {
        return [
            [
                RegionInterface::TITLE,
                '',
                '&quot;' . RegionInterface::TITLE . '&quot; can not be empty.',
            ],
        ];
    }
}
