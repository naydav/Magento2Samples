<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Backend\Test\AssertFormField;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Location\Api\Data\CityInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class NewActionTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/city/new';

    /**
     * @var string
     */
    private $formName = 'city_form';

    public function testEdit()
    {
        $this->dispatch(self::REQUEST_URI);
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        AssertPageTitle::assert($body, 'New City / Location');
        AssertPageHeader::assert($body, 'New City');
        AssertStoreSwitcher::assert($body, false);

        AssertFormField::assert($body, $this->formName, 'general', CityInterface::REGION_ID);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::IS_ENABLED);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::POSITION);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::TITLE);
    }
}
