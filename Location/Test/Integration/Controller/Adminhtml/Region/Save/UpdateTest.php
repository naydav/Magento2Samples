<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;

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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::TITLE => 'region-title-update',
            RegionInterface::IS_ENABLED => false,
            RegionInterface::POSITION => 100,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionById($regionId, 'default');
        AssertArrayContains::assertArrayContains($data, $this->extractData($region));
        $region = $this->getRegionById($regionId, 'test_store');
        AssertArrayContains::assertArrayContains($data, $this->extractData($region));

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $regionId)
        );
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $title = 'region-title-per-store';
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::TITLE => $title,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);

        $region = $this->getRegionById($regionId, 'default');
        self::assertEquals('title-0', $region[RegionInterface::TITLE]);

        $region = $this->getRegionById($regionId, $storeCode);
        self::assertEquals($title, $region[RegionInterface::TITLE]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_store_scope_data.php
     */
    public function testDeleteValueInStoreScope()
    {
        $regionId = 100;
        $storeCode = 'test_store';
        $data = [
            RegionInterface::REGION_ID => $regionId,
            RegionInterface::TITLE => 'per-store-title-0',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'use_default' => [
                RegionInterface::TITLE => 1,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);

        $region = $this->getRegionById($regionId, $storeCode);
        self::assertEquals('title-0', $region[RegionInterface::TITLE]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => 100,
                RegionInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
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
            'form_key' => $this->getFormKey(),
            'general' => [
                RegionInterface::REGION_ID => -1,
                RegionInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
        $this->assertSessionMessages($this->contains('The region does not exist.'), MessageInterface::TYPE_ERROR);
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
