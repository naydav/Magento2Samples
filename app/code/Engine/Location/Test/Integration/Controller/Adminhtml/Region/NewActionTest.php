<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Backend\Test\AssertFormField;
use Engine\Backend\Test\AssertFormFieldset;
use Engine\Backend\Test\AssertPageHeader;
use Engine\Backend\Test\AssertPageTitle;
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
    const REQUEST_URI = 'backend/location/region/new';

    /**
     * @var string
     */
    private $formName = 'engine_region_form';

    public function testEdit()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, 'New Region / Location');
        AssertPageHeader::assert($body, 'New Region');
        AssertStoreSwitcher::assert($body, false);

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::IS_ENABLED);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::TITLE);
        AssertFormFieldset::assert($body, $this->formName, 'cities');
    }
}
