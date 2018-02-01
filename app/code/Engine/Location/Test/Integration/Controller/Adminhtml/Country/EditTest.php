<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Country;

use Engine\Test\Backend\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Location\Api\Data\CountryInterface;
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
    const REQUEST_URI = 'backend/engine-location/country/edit';

    /**
     * @var string
     */
    private $formName = 'engine_location_country_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testEdit()
    {
        $countryId = 100;
        $title = 'Country-name-100';

        $this->dispatch(
            self::REQUEST_URI . '/' . CountryInterface::COUNTRY_ID . '/'
            . $countryId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Country: %1', $title));
        AssertPageHeader::assert($body, __('Edit Country: %1', $title));

        AssertFormField::assert($body, $this->formName, 'general', CountryInterface::ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', CountryInterface::POSITION, 100);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CountryInterface::NAME,
            'Country-name-100'
        );
    }

    public function testEditWithNotExistEntityId()
    {
        $countryId = -1;

        $this->dispatch(
            self::REQUEST_URI . '/' . CountryInterface::COUNTRY_ID . '/'
            . $countryId . '/'
        );

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages(
            $this->contains('Country with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
