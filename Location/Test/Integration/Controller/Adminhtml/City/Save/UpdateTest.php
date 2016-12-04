<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const REQUEST_URI = 'backend/location/city/save/store/%s/back/edit';

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
            CityInterface::TITLE => 'city-title-update',
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 100,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $city = $this->getCityById($cityId, 'default');
        AssertArrayContains::assertArrayContains($data, $this->extractData($city));
        $city = $this->getCityById($cityId, 'test_store');
        AssertArrayContains::assertArrayContains($data, $this->extractData($city));

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $cityId)
        );
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $title = 'city-title-per-store';
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::TITLE => $title,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);

        $city = $this->getCityById($cityId, 'default');
        self::assertEquals('title-0', $city[CityInterface::TITLE]);

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals($title, $city[CityInterface::TITLE]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope_data.php
     */
    public function testDeleteValueInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $data = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::TITLE => 'per-store-title-0',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'use_default' => [
                CityInterface::TITLE => 1,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, $storeCode);
        $this->dispatch($uri);

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals('title-0', $city[CityInterface::TITLE]);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => 100,
                CityInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
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
            'form_key' => $this->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => -1,
                CityInterface::TITLE => 'title-0',
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages($this->contains('The city does not exist.'), MessageInterface::TYPE_ERROR);
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
     * @param int $cityId
     * @param string|null $storeCode
     * @return CityInterface
     */
    private function getCityById($cityId, $storeCode = null)
    {
        if (null !== $storeCode) {
            /** @var StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->get(StoreManagerInterface::class);
            $currentStore = $storeManager->getStore()->getCode();
            $storeManager->setCurrentStore($storeCode);
        }

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $city = $cityRepository->get($cityId);

        if (null !== $storeCode) {
            $storeManager->setCurrentStore($currentStore);
        }
        return $city;
    }

    /**
     * @param CityInterface $city
     * @return array
     */
    private function extractData(CityInterface $city)
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = $this->_objectManager->get(HydratorInterface::class);
        return $hydrator->extract($city);
    }
}
