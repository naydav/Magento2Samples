<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

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

    public function testEdit()
    {
        $this->dispatch(self::REQUEST_URI);
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp('title', "#^New Region / Location.*#", 1, $body, 'Meta title is wrong');
        self::assertSelectEquals('h1', 'New Region', 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 0, $body, 'Store view change button is not be present');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*region_form.*#", 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', '#.*"cities":{"type":"fieldset".*#', 1, $body, 'Cities data is missed');
    }
}
