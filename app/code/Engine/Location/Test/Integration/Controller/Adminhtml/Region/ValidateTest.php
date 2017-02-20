<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class ValidateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/region/validate/store/0';

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
     * @param array $data
     * @dataProvider successfulfailedValidationDataProvider
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testSuccessfulValidation(array $data)
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);
    }

    /**
     * @return array
     */
    public function successfulfailedValidationDataProvider()
    {
        return [
            'on_create' => [
                [
                    RegionInterface::IS_ENABLED => true,
                    RegionInterface::POSITION => 100,
                    RegionInterface::TITLE => 'Region-title',
                ],
            ],
            'on_update' => [
                [
                    RegionInterface::REGION_ID => 100,
                    RegionInterface::IS_ENABLED => false,
                    RegionInterface::POSITION => 1000,
                    RegionInterface::TITLE => 'Region-title-edit',
                ],
            ],
        ];
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
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
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
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'Region-title-edit',
        ];
        $data[$field] = $value;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
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
                RegionInterface::TITLE,
                '',
                '"' . RegionInterface::TITLE . '" can not be empty.',
            ],
        ];
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testValidateNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => 100,
                RegionInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testValidateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => 100,
                RegionInterface::IS_ENABLED => false,
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
}
