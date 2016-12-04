<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

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
    const REQUEST_URI = 'backend/location/city/new';

    public function testEdit()
    {
        $this->dispatch(self::REQUEST_URI);
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);
        self::assertSelectRegExp('title', "#^New City / Location.*#", 1, $body, 'Meta title is wrong');
        self::assertSelectEquals('h1', 'New City', 1, $body, 'Page title is wrong');
        self::assertSelectCount('#store-change-button', 0, $body, 'Store view change button is not be present');
        self::assertSelectCount('.form-inline', 1, $body, 'Form is missed');
        self::assertSelectRegExp('script', "#.*city_form.*#", 1, $body, 'Form is missed');
    }
}
