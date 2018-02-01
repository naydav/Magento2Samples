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
 */
class NewActionTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/country/new';

    /**
     * @var string
     */
    private $formName = 'engine_location_country_form';

    public function testNew()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('New Country'));
        AssertPageHeader::assert($body, __('New Country'));

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CountryInterface::ENABLED
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CountryInterface::POSITION
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CountryInterface::NAME
        );
    }
}
