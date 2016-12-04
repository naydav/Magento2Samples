<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    public function testExecute()
    {
        $this->dispatch('backend/location/city/index');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp('title', '#^Manage Cities / Location.*#', 1, $body, 'Meta title is wrong');
        self::assertSelectEquals('h1', 'Manage Cities', 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('#add', 1, $body, 'Add new button is missed');
        self::assertSelectCount('#page:main-container .admin__data-grid-outer-wrap', 1, $body, 'Grid is missed');
    }
}
