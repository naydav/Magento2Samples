<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Backend\Test\AssertFormField;
use Engine\Test\AssertPageHeader;
use Engine\Test\AssertPageTitle;
use Engine\Backend\Test\AssertStoreSwitcher;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class NewActionTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/new';

    /**
     * @var string
     */
    private $formName = 'engine_characteristic_group_form';

    public function testNew()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('New Characteristic Group'));
        AssertPageHeader::assert($body, __('New Characteristic Group'));
        AssertStoreSwitcher::assert($body, false);

        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::IS_ENABLED
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::BACKEND_TITLE
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::TITLE
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::DESCRIPTION
        );
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristics',
            'assigned_characteristics'
        );
    }
}
