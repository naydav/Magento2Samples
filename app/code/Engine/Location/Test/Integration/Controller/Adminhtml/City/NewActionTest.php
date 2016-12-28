<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Backend\Test\AssertFormField;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

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
    private $formName = 'engine_city_form';

    public function testEdit()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

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
