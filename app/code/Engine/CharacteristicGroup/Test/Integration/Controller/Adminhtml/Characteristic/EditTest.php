<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\Characteristic;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Characteristic\Api\Data\CharacteristicInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-characteristic/characteristic/edit';

    /**
     * @var string
     */
    private $formName = 'engine_characteristic_form';

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    public function setUp()
    {
        parent::setUp();
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_characteristic_structure.php
     */
    public function testEdit()
    {
        $characteristicGroupId = 200;

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicInterface::CHARACTERISTIC_ID . '/'
            . $characteristicGroupId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristic_groups',
            'assigned_characteristic_groups',
            [
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-200',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-300',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
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

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicInterface::CHARACTERISTIC_ID . '/'
            . $characteristicGroupId . '/store/' . $storeCode . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristic_groups',
            'assigned_characteristic_groups',
            [
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 200,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-200-per-store',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 300,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-300-per-store',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
            ]
        );
    }
}
