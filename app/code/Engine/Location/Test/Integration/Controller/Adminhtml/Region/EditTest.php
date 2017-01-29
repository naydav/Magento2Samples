<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Backend\Test\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\Location\Api\Data\CityInterface;
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
    const REQUEST_URI = 'backend/engine-location/region/edit';

    /**
     * @var string
     */
    private $formName = 'engine_location_region_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_200.php
     */
    public function testEdit()
    {
        $regionId = 100;
        $title = 'Region-title-100';

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
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION, 100);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::TITLE,
            $title
        );
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'cities',
            'assigned_cities',
            [
                [
                    CityInterface::CITY_ID => 100,
                    CityInterface::TITLE => 'City-title-100',
                    CityInterface::IS_ENABLED => 1,
                ],
                [
                    CityInterface::CITY_ID => 200,
                    CityInterface::TITLE => 'City-title-200',
                    CityInterface::IS_ENABLED => 1,
                ],
            ]
        );
     }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100_store_scope.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_100.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_id_200.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $regionId = 100;
        $title = 'Region-title-100-per-store';

        $this->dispatch(
            self::REQUEST_URI . '/' . RegionInterface::REGION_ID . '/'
            . $regionId . '/store/' . $storeCode . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Region: %1', $title));
        AssertPageHeader::assert($body, __('Edit Region: %1', $title));
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::IS_ENABLED, true);
        AssertFormField::assert($body, $this->formName, 'general', RegionInterface::POSITION, 100);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            RegionInterface::TITLE,
            $title
        );
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'cities',
            'assigned_cities',
            [
                [
                    CityInterface::CITY_ID => 100,
                    CityInterface::TITLE => 'City-title-100',
                    CityInterface::IS_ENABLED => 1,
                ],
                [
                    CityInterface::CITY_ID => 200,
                    CityInterface::TITLE => 'City-title-200',
                    CityInterface::IS_ENABLED => 1,
                ],
            ]
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
