<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

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
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testEdit()
    {
        $regionId = 100;
        $regionTitle = 'title-0';

        $this->dispatch(self::REQUEST_URI . '/region_id/' . $regionId . '/');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp(
            'title',
            "#^Edit Region: {$regionTitle} / Location.*#",
            1,
            $body,
            'Meta title is wrong'
        );
        self::assertSelectEquals('h1', "Edit Region: {$regionTitle}", 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*\"title\":\"{$regionTitle}\".*#", 1, $body, 'Title data is missed');
        self::assertSelectRegExp('script', '#.*"cities":{"type":"fieldset".*#', 1, $body, 'Cities data is missed');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_store_scope_data.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $regionId = 100;
        $regionTitle = 'per-store-title-0';

        $this->dispatch(self::REQUEST_URI . '/region_id/' . $regionId . '/store/' . $storeCode . '/');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp(
            'title',
            "#^Edit Region: {$regionTitle} / Location.*#",
            1,
            $body,
            'Meta title is wrong'
        );
        self::assertSelectEquals('h1', "Edit Region: {$regionTitle}", 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*\"title\":\"{$regionTitle}\".*#", 1, $body, 'Title data is missed');
    }

    public function testEditWithNotExistEntityId()
    {
        $regionId = -1;

        $this->dispatch(self::REQUEST_URI . '/region_id/' . $regionId . '/');

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
        $this->assertSessionMessages(
            $this->contains('Region with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
