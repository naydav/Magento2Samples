<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Backend\Test\AssertArrayContains;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
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
    const REQUEST_URI = 'backend/location/region/save/store/%s/back/edit';

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
        $this->regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'region-title-update',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionById($regionId, 'default');
        AssertArrayContains::assert($data, $this->hydrator->extract($region));
        $region = $this->getRegionById($regionId, 'test_store');
        AssertArrayContains::assert($data, $this->hydrator->extract($region));

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $regionId)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $dataPerScope = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'region-title-per-store',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $dataPerScope,
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionById($regionId, 'default');
        $dataForGlobalScope = array_merge($dataPerScope, [
            RegionInterface::TITLE => 'title-0',
        ]);
        AssertArrayContains::assert($dataForGlobalScope, $this->hydrator->extract($region));

        $region = $this->getRegionById($regionId, $storeCode);
        AssertArrayContains::assert($dataPerScope, $this->hydrator->extract($region));

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $regionId . '/store/' . $storeCode)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => $regionId,
                RegionInterface::TITLE => 'per-store-title-0',
            ],
            'use_default' => [
                RegionInterface::TITLE => 1,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionById($regionId, $storeCode);
        self::assertEquals('title-0', $region[RegionInterface::TITLE]);

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $regionId . '/store/' . $storeCode)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => 100,
                RegionInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/location/region'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => -1,
                RegionInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/location/region'));
        $this->assertSessionMessages($this->contains('The Region does not exist.'), MessageInterface::TYPE_ERROR);
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
