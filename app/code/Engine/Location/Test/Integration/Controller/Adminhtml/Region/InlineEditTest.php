<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Test\AssertArrayContains;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-location/region/inlineEdit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->regionRepository = $this->_objectManager->get(
            RegionRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testInlineEditInGlobalScope()
    {
        $regionId = 100;
        $itemData = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'Region-title-inline-edit',
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

        $region = $this->getRegionById($regionId, 'default');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($region));
        $region = $this->getRegionById($regionId, 'test_store');
        AssertArrayContains::assert($itemData, $this->hydrator->extract($region));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100_store_scope.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $regionId = 100;
        $itemDataForTestStore = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'Region-title-inline-edit-per-store',
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

        $region = $this->getRegionById($regionId, 'default');
        $itemDataForDefaultStore = array_merge($itemDataForTestStore, [
            RegionInterface::TITLE => 'Region-title-100',
        ]);
        AssertArrayContains::assert($itemDataForDefaultStore, $this->hydrator->extract($region));

        $region = $this->getRegionById($regionId, $storeCode);
        AssertArrayContains::assert($itemDataForTestStore, $this->hydrator->extract($region));
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $regionId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    RegionInterface::REGION_ID => $regionId,
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
            "[ID: {$regionId}] The Region does not exist.",
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'items' => [
                [
                    RegionInterface::REGION_ID => 100,
                    RegionInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
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
                    RegionInterface::REGION_ID => 100,
                    RegionInterface::IS_ENABLED => false,
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     * @dataProvider failedValidationDataProvider
     */
    public function testFailedValidation($field, $value, $errorMessage)
    {
        $regionId = 100;
        $itemData = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'Region-title-inline-edit',
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
                RegionInterface::TITLE,
                '',
                '"' . RegionInterface::TITLE . '" can not be empty.',
            ],
        ];
    }

    /**
     * @param int $regionId
     * @param string|null $storeCode
     * @return RegionInterface
     */
    private function getRegionById($regionId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $currentStore = $this->storeManager->getStore()->getCode();
            $this->storeManager->setCurrentStore($storeCode);
        }

        $region = $this->regionRepository->get($regionId);

        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($currentStore);
        }
        return $region;
    }
}
