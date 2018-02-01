<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class ValidationTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/region/save';

    /**
     * @var FormKey
     */
    private $formKey;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidationOnCreate(string $field, string $value, string $errorMessage)
    {
        $data = [
            RegionInterface::COUNTRY_ID => 100,
            RegionInterface::ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::NAME => 'Region-name',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $errorMessage
     * @dataProvider failedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
     */
    public function testFailedValidationOnUpdate(string $field, string $value, string $errorMessage)
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::COUNTRY_ID => 100,
            RegionInterface::ENABLED => false,
            RegionInterface::POSITION => 100,
            RegionInterface::NAME => 'Region-name-updated',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->contains($errorMessage), MessageInterface::TYPE_ERROR);
    }

    /**
     * @return array
     */
    public function failedValidationDataProvider(): array
    {
        return [
            'empty_name' => [
                RegionInterface::NAME,
                '',
                '&quot;' . RegionInterface::NAME . '&quot; can not be empty.',
            ],
        ];
    }
}
