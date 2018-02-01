<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Test\Backend\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Location\Api\Data\CityInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/city/edit';

    /**
     * @var string
     */
    private $formName = 'engine_location_city_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city.php
     */
    public function testEdit()
    {
        $cityId = 100;
        $title = 'City-name-100';

        $this->dispatch(
            self::REQUEST_URI . '/' . CityInterface::CITY_ID . '/'
            . $cityId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit City: %1', $title));
        AssertPageHeader::assert($body, __('Edit City: %1', $title));

        AssertFormField::assert($body, $this->formName, 'general', CityInterface::REGION_ID, 100);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CityInterface::POSITION, 100);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CityInterface::NAME,
            'City-name-100'
        );
    }

    public function testEditWithNotExistEntityId()
    {
        $cityId = -1;

        $this->dispatch(
            self::REQUEST_URI . '/' . CityInterface::CITY_ID . '/'
            . $cityId . '/'
        );

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages(
            $this->contains('City with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
