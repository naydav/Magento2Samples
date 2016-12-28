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
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/region/edit';

    /**
     * @var string
     */
    private $formName = 'engine_region_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testEdit()
    {
        $regionId = 100;
        $title = 'title-0';

        $this->dispatch(self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/' . $regionId . '/');
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, "Edit Region: {$title} / Location");
        AssertPageHeader::assert($body, "Edit Region: {$title}");
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION, 200);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::TITLE, $title);
        AssertFormFieldset::assert($body, $this->formName, 'cities');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_store_scope.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $regionId = 100;
        $title = 'per-store-title-0';

        $this->dispatch(
            self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/' . $regionId . '/store/' . $storeCode . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, "Edit Region: {$title} / Location");
        AssertPageHeader::assert($body, "Edit Region: {$title}");
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION, 200);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::TITLE, $title);
        AssertFormFieldset::assert($body, $this->formName, 'cities');
    }

    public function testEditWithNotExistEntityId()
    {
        $regionId = -1;

        $this->dispatch(self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/' . $regionId . '/');

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/location/region'));
        $this->assertSessionMessages(
            $this->contains('Region with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
