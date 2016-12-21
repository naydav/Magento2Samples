<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Backend\Test\AssertFormField;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/city/edit';

    /**
     * @var string
     */
    private $formName = 'city_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testEdit()
    {
        $cityId = 100;
        $title = 'title-0';

        $this->dispatch(self::REQUEST_URI . '/' . CityInterface::CITY_ID . '/' . $cityId . '/');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        AssertPageTitle::assert($body, "Edit City: {$title} / Location");
        AssertPageHeader::assert($body, "Edit City: {$title}");
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', CityInterface::REGION_ID, 100);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::POSITION, 200);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::TITLE, $title);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $cityId = 100;
        $title = 'per-store-title-0';

        $this->dispatch(
            self::REQUEST_URI . '/' . CityInterface::CITY_ID . '/' . $cityId . '/store/' . $storeCode . '/'
        );
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        AssertPageTitle::assert($body, "Edit City: {$title} / Location");
        AssertPageHeader::assert($body, "Edit City: {$title}");
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', CityInterface::REGION_ID, 100);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::POSITION, 200);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::TITLE, $title);
    }

    public function testEditWithNotExistEntityId()
    {
        $cityId = -1;

        $this->dispatch(self::REQUEST_URI . '/' . CityInterface::CITY_ID . '/' . $cityId . '/');

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages(
            $this->contains('City with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
