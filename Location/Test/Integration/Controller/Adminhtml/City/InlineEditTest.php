<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
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
    const REQUEST_URI = 'backend/location/city/inlineEdit';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testInlineEdit()
    {
        $cityId = 100;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => $cityId,
                    CityInterface::IS_ENABLED => false,
                    CityInterface::POSITION => 1000,
                    CityInterface::TITLE => 'inline-edit-title',
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $city = $this->getCityById($cityId);
        self::assertEquals(false, $city->getIsEnabled());
        self::assertEquals(1000, $city->getPosition());
        self::assertEquals('inline-edit-title', $city->getTitle());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope_data.php
     */
    public function testInlineEditInStoreScope()
    {
        $storeCode = 'test_store';
        $cityId = 100;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => $cityId,
                    CityInterface::IS_ENABLED => false,
                    CityInterface::POSITION => 1000,
                    CityInterface::TITLE => 'inline-edit-title-per-scope',
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI . '/store/' . $storeCode . '/');

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(0, $jsonResponse->error);
        self::assertEmpty($jsonResponse->messages);

        $city = $this->getCityById($cityId, 'default');
        self::assertEquals('title-0', $city->getTitle());

        $city = $this->getCityById($cityId, $storeCode);
        self::assertEquals(false, $city->getIsEnabled());
        self::assertEquals(1000, $city->getPosition());
        self::assertEquals('inline-edit-title-per-scope', $city->getTitle());
    }

    public function testInlineEditWithNotExistEntityId()
    {
        $cityId = -1;

        $request = $this->getRequest();
        $request->getHeaders()->addHeaderLine('X_REQUESTED_WITH', 'XMLHttpRequest');
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => $cityId,
                ],
            ],
        ]);

        $this->dispatch(self::REQUEST_URI);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $jsonResponse = json_decode($body);
        self::assertNotEmpty($jsonResponse);
        self::assertEquals(1, $jsonResponse->error);
        self::assertContains("[ID: {$cityId}] The City does not exist.", $jsonResponse->messages);
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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testInlineEditNoAjaxRequest()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'items' => [
                [
                    CityInterface::CITY_ID => 100,
                    CityInterface::IS_ENABLED => false,
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
}
