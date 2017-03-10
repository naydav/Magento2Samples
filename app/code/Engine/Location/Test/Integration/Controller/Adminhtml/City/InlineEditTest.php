<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Test\AssertArrayContains;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-location/city/inlineEdit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->cityRepository = $this->_objectManager->get(
            CityRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testInlineEditInGlobalScope()
    {
        $cityId = 100;
        $itemData = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 1000,
            CityInterface::TITLE => 'City-title-inline-edit',
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

        $city = $this->getCityById($cityId, 'default');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($city));
        $city = $this->getCityById($cityId, 'test_store');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($city));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100_store_scope.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $cityId = 100;
        $itemDataForTestStore = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 1000,
            CityInterface::TITLE => 'City-title-inline-edit-per-store',
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

        $city = $this->getCityById($cityId, 'default');
        $itemDataForDefaultStore = array_merge($itemDataForTestStore, [
            CityInterface::TITLE => 'City-title-100',
        ]);
        AssertArrayContains::assert($itemDataForDefaultStore, $this->hydrator->extract($city));

        $city = $this->getCityById($cityId, $storeCode);
        AssertArrayContains::assert($itemDataForTestStore, $this->hydrator->extract($city));
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $cityId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => $cityId,
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
            "[ID: {$cityId}] The City does not exist.",
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     */
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => 100,
                    CityInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
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
                    CityInterface::CITY_ID => 100,
                    CityInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidation($field, $value, $errorMessage)
    {
        $cityId = 100;
        $itemData = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 1000,
            CityInterface::TITLE => 'City-title-inline-edit',
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
                CityInterface::TITLE,
                '',
                '"' . CityInterface::TITLE . '" can not be empty.',
            ],
        ];
    }

    /**
     * @param int $cityId
     * @param string|null $storeCode
     * @return CityInterface
     */
    private function getCityById($cityId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($storeCode);
        }

        $city = $this->cityRepository->get($cityId);
        return $city;
    }
}
