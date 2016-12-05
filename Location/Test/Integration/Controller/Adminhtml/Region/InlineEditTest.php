<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Test\AssertArrayContains;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class InlineEditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/region/inlineEdit';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testInlineEdit()
    {
        $regionId = 100;
        $itemData = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'inline-edit-title',
        ];

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                $itemData,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $region = $this->getRegionById($regionId);
        AssertArrayContains::assertArrayContains($itemData, $this->extractData($region));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_store_scope_data.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $regionId = 100;
        $itemDataPerScope = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 1000,
            RegionInterface::TITLE => 'inline-edit-title-per-scope',
        ];

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                $itemDataPerScope,
            ],
        ]);

        $this->dispatch(self::REQUEST_URI . '/store/' . $storeCode . '/');

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $region = $this->getRegionById($regionId, 'default');
        $itemDataForGlobalScope = array_merge($itemDataPerScope, [
            RegionInterface::TITLE => 'title-0',
        ]);
        AssertArrayContains::assertArrayContains($itemDataForGlobalScope, $this->extractData($region));

        $region = $this->getRegionById($regionId, $storeCode);
        AssertArrayContains::assertArrayContains($itemDataPerScope, $this->extractData($region));
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $regionId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    RegionInterface::REGION_ID => $regionId,
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains("[ID: {$regionId}] The Region does not exist.", $jsonResponse->messages);
    }

    public function testInlineEditWithEmptyItems()
    {
        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains('Please correct the data sent.', $jsonResponse->messages);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    RegionInterface::REGION_ID => 100,
                    RegionInterface::IS_ENABLED => false,
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains('Please correct the data sent.', $jsonResponse->messages);
    }

    /**
     * @return string
     */
    private function getFormKey()
    {
        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        return $formKey->getFormKey();
    }

    /**
     * @param int $regionId
     * @param string|null $storeCode
     * @return RegionInterface
     */
    private function getRegionById($regionId, $storeCode = null)
    {
        if (null !== $storeCode) {
            /** @var StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->get(StoreManagerInterface::class);
            $currentStore = $storeManager->getStore()->getCode();
            $storeManager->setCurrentStore($storeCode);
        }

        /** @var RegionRepositoryInterface $regionRepository */
        $regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $region = $regionRepository->get($regionId);

        if (null !== $storeCode) {
            $storeManager->setCurrentStore($currentStore);
        }
        return $region;
    }

    /**
     * @param RegionInterface $region
     * @return array
     */
    private function extractData(RegionInterface $region)
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = $this->_objectManager->get(HydratorInterface::class);
        return $hydrator->extract($region);
    }
}
