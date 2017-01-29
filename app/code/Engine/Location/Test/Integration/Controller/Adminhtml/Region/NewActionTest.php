<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Backend\Test\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Location\Api\Data\RegionInterface;
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
    const REQUEST_URI = 'backend/engine-location/region/new';

    /**
     * @var string
     */
    private $formName = 'engine_location_region_form';

    public function testNew()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('New Region'));
        AssertPageHeader::assert($body, __('New Region'));
        AssertStoreSwitcher::assert($body, false);

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::IS_ENABLED
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::POSITION
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::TITLE
        );
    }
}
