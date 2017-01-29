<?php
namespace Engine\CharacteristicGroup\Test\Integration\Backend;

use Engine\Backend\Test\AssertMenuItem;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MenuTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/backend';

    public function testMenu()
    {
        $this->dispatch(self::REQUEST_URI);
        $body = $this->getResponse()->getBody();

        self::assertNotEmpty($body);

        AssertMenuItem::assert(
            $body,
            'engine-characteristicgroup-characteristic-group',
            'Characteristic Groups'
        );

        AssertMenuItem::assert(
            $body,
            'engine-characteristicgroup-characteristic-group-index',
            'Characteristic Group List',
            'engine-characteristic-group/characteristicGroup'
        );
        AssertMenuItem::assert(
            $body,
            'engine-characteristicgroup-characteristic-group-new',
            'Add Characteristic Group',
            'engine-characteristic-group/characteristicGroup/new'
        );
    }
}
