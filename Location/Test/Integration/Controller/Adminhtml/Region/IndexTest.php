<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class IndexTest extends AbstractBackendController
{
    public function testExecute()
    {
        $this->dispatch('backend/location/region/index');
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp('title', '#^Manage Regions / Location.*#', 1, $body, 'Meta title is wrong');
        self::assertSelectEquals('h1', 'Manage Regions', 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 1, $body, 'Store view change button is missed');
        self::assertSelectCount('#add', 1, $body, 'Add new button is missed');
        self::assertSelectCount('#page:main-container .admin__data-grid-outer-wrap', 1, $body, 'Grid is missed');
    }
}
