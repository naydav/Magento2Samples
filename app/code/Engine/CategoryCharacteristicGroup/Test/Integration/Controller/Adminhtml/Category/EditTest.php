<?php
namespace Engine\CategoryCharacteristicGroup\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class EditTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-category/category/edit';

    /**
     * @var string
     */
    private $formName = 'engine_category_form';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testEdit()
    {
        $categoryId = 200;

        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $categoryId . '/'
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
                    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-200',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100',
                    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-100',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
            ]
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure_store_scope.php
     */
    public function testEditInStoreScope()
    {
        $storeCode = 'test_store';
        $categoryId = 200;

        $this->dispatch(
            self::REQUEST_URI . '/' . CategoryInterface::CATEGORY_ID . '/'
            . $categoryId . '/store/' . $storeCode . '/'
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
                    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-200',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
                [
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID => 100,
                    CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title-100-per-store',
                    CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle-100',
                    CharacteristicGroupInterface::IS_ENABLED => 1,
                ],
            ]
        );
    }
}
