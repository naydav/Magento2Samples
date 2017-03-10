<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Location\Controller\Adminhtml\City\Save;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
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
    const REQUEST_URI = 'backend/engine-location/city/save/store/%s/back/edit';

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

    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->cityRepository = $this->_objectManager->get(
            CityRepositoryInterface::class
        );
        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
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
            CityInterface::POSITION => 200,
            CityInterface::TITLE => 'City-title-updated',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-location/city/edit/city_id/'
                . $cityId
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The City has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $cityId,
            $this->registry->registry(Save::REGISTRY_CITY_ID_KEY)
        );

        $city = $this->getCityById($cityId, 'default');
        AssertArrayContains::assert($data, $this->hydrator->extract($city));
        $city = $this->getCityById($cityId, 'test_store');
        AssertArrayContains::assert($data, $this->hydrator->extract($city));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_200.php
     * @magentoDataFixture ../../../../app/code/Engine/PerStoreDataSupport/Test/_files/store.php
     */
    public function testUpdateInStoreScope()
    {
        $cityId = 100;
        $storeCode = 'test_store';
        $dataForTestStore = [
            CityInterface::CITY_ID => $cityId,
            CityInterface::REGION_ID => 200,
            CityInterface::IS_ENABLED => false,
            CityInterface::POSITION => 200,
            CityInterface::TITLE => 'City-title-per-store',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $dataForTestStore,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, $storeCode));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-location/city/edit/city_id/'
                . $cityId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The City has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(
            $cityId,
            $this->registry->registry(Save::REGISTRY_CITY_ID_KEY)
        );

        $city = $this->getCityById($cityId, 'default');
        $dataForDefaultStore = array_merge($dataForTestStore, [
            CityInterface::TITLE => 'City-title-100',
        ]);
        AssertArrayContains::assert($dataForDefaultStore, $this->hydrator->extract($city));

        $city = $this->getCityById($cityId, $storeCode);
        AssertArrayContains::assert($dataForTestStore, $this->hydrator->extract($city));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100_store_scope.php
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
                CityInterface::TITLE => 'City-title-per-store',
                '_use_default' => [
                    CityInterface::TITLE => 1,
                ],
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, $storeCode));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect(
            $this->stringContains(
                'backend/engine-location/city/edit/city_id/'
                . $cityId . '/store/' . $storeCode
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The City has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $city = $this->getCityById($cityId, $storeCode);
        $expectedData = [
            CityInterface::TITLE => 'City-title-100',
        ];
        AssertArrayContains::assert($expectedData, $this->hydrator->extract($city));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => 100,
                CityInterface::TITLE => 'City-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CITY_ID_KEY));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     */
    public function testUpdateWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::CITY_ID => -1,
                CityInterface::TITLE => 'City-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages(
            $this->contains('The City does not exist.'),
            MessageInterface::TYPE_ERROR
        );
        self::assertNull($this->registry->registry(Save::REGISTRY_CITY_ID_KEY));
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
