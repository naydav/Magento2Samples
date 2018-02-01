<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Test\Backend\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Location\Api\Data\RegionInterface;
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
    const REQUEST_URI = 'backend/engine-location/region/edit';

    /**
     * @var string
     */
    private $formName = 'engine_location_region_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region.php
     */
    public function testEdit()
    {
        $regionId = 100;
        $title = 'Region-name-100';

        $this->dispatch(
            self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/'
            . $regionId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Region: %1', $title));
        AssertPageHeader::assert($body, __('Edit Region: %1', $title));

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::COUNTRY_ID, 100);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION, 100);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::NAME,
            'Region-name-100'
        );
    }

    public function testEditWithNotExistEntityId()
    {
        $regionId = -1;

        $this->dispatch(
            self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/'
            . $regionId . '/'
        );

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages(
            $this->contains('Region with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
