<?php
namespace Engine\CategoryCharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\Backend\Test\AssertFormDynamicRows;
use Engine\Category\Api\CategoryRepositoryInterface;
use Engine\Category\Api\Data\CategoryInterface;
use Engine\Category\Api\RootCategoryIdProviderInterface;
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
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/edit';

    /**
     * @var string
     */
    private $formName = 'engine_characteristic_group_form';

    /**
     * @var RootCategoryIdProviderInterface
     */
    private $rootCategoryIdProvider;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function setUp()
    {
        parent::setUp();
        $this->rootCategoryIdProvider = $this->_objectManager->get(RootCategoryIdProviderInterface::class);
        $this->categoryRepository = $this->_objectManager->get(CategoryRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CategoryCharacteristicGroup/Test/_files/category_group_structure.php
     */
    public function testEdit()
    {
        $characteristicGroupId = 200;

        $this->dispatch(
            self::REQUEST_URI . '/' . CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID . '/'
            . $characteristicGroupId . '/'
        );
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        $rootCategory = $this->categoryRepository->get($this->rootCategoryIdProvider->provide());
        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'categories',
            'assigned_categories',
            [
                [
                    CategoryInterface::CATEGORY_ID => 200,
                    CategoryInterface::TITLE => 'Category-title-200',
                    CategoryInterface::PARENT_ID => sprintf(
                        '%s (ID: %d)',
                        $rootCategory->getTitle(),
                        $rootCategory->getCategoryId()
                    ),
                    CategoryInterface::IS_ENABLED => 1,
                ],
                [
                    CategoryInterface::CATEGORY_ID => 300,
                    CategoryInterface::TITLE => 'Category-title-300',
                    CategoryInterface::PARENT_ID => sprintf(
                        '%s (ID: %d)',
                        $rootCategory->getTitle(),
                        $rootCategory->getCategoryId()
                    ),
                    CategoryInterface::IS_ENABLED => 1,
                ],
            ]
        );
    }
}
