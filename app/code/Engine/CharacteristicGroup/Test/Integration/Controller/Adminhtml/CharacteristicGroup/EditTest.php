<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Backend\Test\AssertFormField;
use Engine\Characteristic\Api\Data\CharacteristicInterface;
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
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/edit';

    /**
     * @var string
     */
    private $formName = 'engine_characteristic_group_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testEdit()
    {
        $characteristicGroupId = 200;
        $title = 'CharacteristicGroup-title-200';

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID . '/'
            . $characteristicGroupId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Characteristic Group: %1', $title));
        AssertPageHeader::assert($body, __('Edit Characteristic Group: %1', $title));
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', CharacteristicGroupInterface::IS_ENABLED, true);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::BACKEND_TITLE,
            'CharacteristicGroup-backendTitle-200'
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::TITLE,
            $title
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::DESCRIPTION,
            'CharacteristicGroup-description-200'
        );
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristics',
            'assigned_characteristics',
            [
                [
                    CharacteristicInterface::CHARACTERISTIC_ID => 200,
                    CharacteristicInterface::IS_ENABLED => 1,
                    CharacteristicInterface::BACKEND_TITLE => 'Characteristic-backendTitle-200',
                    CharacteristicInterface::TITLE => 'Characteristic-title-200',
                ],
                [
                    CharacteristicInterface::CHARACTERISTIC_ID => 100,
                    CharacteristicInterface::IS_ENABLED => 1,
                    CharacteristicInterface::BACKEND_TITLE => 'Characteristic-backendTitle-100',
                    CharacteristicInterface::TITLE => 'Characteristic-title-100',
                ],
            ]
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure_store_scope.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $characteristicGroupId = 200;
        $title = 'CharacteristicGroup-title-200-per-store';

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID . '/'
            . $characteristicGroupId . '/store/' . $storeCode . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertPageTitle::assert($body, __('Edit Characteristic Group: %1', $title));
        AssertPageHeader::assert($body, __('Edit Characteristic Group: %1', $title));
        AssertStoreSwitcher::assert($body);

        AssertFormField::assert($body, $this->formName, 'general', CharacteristicGroupInterface::IS_ENABLED, true);
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::BACKEND_TITLE,
            'CharacteristicGroup-backendTitle-200'
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::TITLE,
            $title
        );
        AssertFormField::assert(
            $body,
            $this->formName,
            'general',
            CharacteristicGroupInterface::DESCRIPTION,
            'CharacteristicGroup-description-200-per-store'
        );
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristics',
            'assigned_characteristics',
            [
                [
                    CharacteristicInterface::CHARACTERISTIC_ID => 200,
                    CharacteristicInterface::IS_ENABLED => 1,
                    CharacteristicInterface::BACKEND_TITLE => 'Characteristic-backendTitle-200',
                    CharacteristicInterface::TITLE => 'Characteristic-title-200-per-store',
                ],
                [
                    CharacteristicInterface::CHARACTERISTIC_ID => 100,
                    CharacteristicInterface::IS_ENABLED => 1,
                    CharacteristicInterface::BACKEND_TITLE => 'Characteristic-backendTitle-100',
                    CharacteristicInterface::TITLE => 'Characteristic-title-100-per-store',
                ],
            ]
        );
    }

    public function testEditWithNotExistEntityId()
    {
        $characteristicGroupId = -1;

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID . '/'
            . $characteristicGroupId . '/'
        );

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages(
            $this->contains('Characteristic Group with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
