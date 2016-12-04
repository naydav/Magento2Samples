<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

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
    const REQUEST_URI = 'backend/location/city/edit';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     */
    public function testEdit()
    {
        $cityId = 100;
        $cityTitle = 'title-0';

        $this->dispatch(self::REQUEST_URI . '/city_id/' . $cityId . '/');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp(
            'title',
            "#^Edit City: {$cityTitle} / Location.*#",
            1,
            $body,
            'Meta title is wrong'
        );
        self::assertSelectEquals('h1', "Edit City: {$cityTitle}", 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*\"title\":\"{$cityTitle}\".*#", 1, $body, 'Title data is missed');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_store_scope_data.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $cityId = 100;
        $cityTitle = 'per-store-title-0';

        $this->dispatch(self::REQUEST_URI . '/city_id/' . $cityId . '/store/' . $storeCode . '/');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp(
            'title',
            "#^Edit City: {$cityTitle} / Location.*#",
            1,
            $body,
            'Meta title is wrong'
        );
        self::assertSelectEquals('h1', "Edit City: {$cityTitle}", 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*\"title\":\"{$cityTitle}\".*#", 1, $body, 'Title data is missed');
    }

    public function testEditWithNotExistEntityId()
    {
        $cityId = -1;

        $this->dispatch(self::REQUEST_URI . '/city_id/' . $cityId . '/');

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages(
            $this->contains('City with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
