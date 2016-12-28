<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Backend\Test\AssertArrayContains;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const REQUEST_URI = 'backend/location/city/save/store/%s/back/edit';

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
        $this->cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_200.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInGlobalScope()
    {
        $cityId = 100;
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 200,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 100,
            CityInterface::TITLE => 'city-title-update',
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
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);

        $city = $this->getCityById($cityId, 'default');
        AssertArrayContains::assert($data, $this->hydrator->extract($city));
        $city = $this->getCityById($cityId, 'test_store');
        AssertArrayContains::assert($data, $this->hydrator->extract($city));

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $cityId)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_200.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $dataPerScope = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 200,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 100,
            CityInterface::TITLE => 'city-title-per-store',
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
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);

        $city = $this->getCityById($cityId, 'default');
        $dataForGlobalScope = array_merge($dataPerScope, [
            CityInterface::TITLE => 'title-0',
        ]);
        AssertArrayContains::assert($dataForGlobalScope, $this->hydrator->extract($city));

        $city = $this->getCityById($cityId, $storeCode);
        AssertArrayContains::assert($dataPerScope, $this->hydrator->extract($city));

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $cityId . '/store/' . $storeCode)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope.php
     */
    public function testDeleteValueInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => $cityId,
                CityInterface::TITLE => 'per-store-title-0',
            ],
            'use_default' => [
                CityInterface::TITLE => 1,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);
        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals('title-0', $city[CityInterface::TITLE]);

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $cityId . '/store/' . $storeCode)
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => 100,
                CityInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/location/city'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => -1,
                CityInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/location/city'));
        $this->assertSessionMessages($this->contains('The City does not exist.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @param int $cityId
     * @param string|null $storeCode
     * @return CityInterface
     */
    private function getCityById($cityId, $storeCode = null)
    {
        if (null !== $storeCode) {
            $currentStore = $this->storeManager->getStore()->getCode();
            $this->storeManager->setCurrentStore($storeCode);
        }

        $city = $this->cityRepository->get($cityId);

        if (null !== $storeCode) {
            $this->storeManager->setCurrentStore($currentStore);
        }
        return $city;
    }
}
