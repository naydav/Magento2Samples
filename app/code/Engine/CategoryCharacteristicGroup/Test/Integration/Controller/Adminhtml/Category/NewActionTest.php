<?php
namespace Engine\CategoryCharacteristicGroup\Test\Integration\Controller\Adminhtml\Category;

use Engine\Backend\Test\AssertFormDynamicRows;
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
    const REQUEST_URI = 'backend/engine-category/category/new';

    /**
     * @var string
     */
    private $formName = 'engine_category_form';

    public function testNew()
    {
        $this->dispatch(self::REQUEST_URI);
        self::assertEquals(Response::STATUS_CODE_200, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $body = $this->getResponse()->getBody();
        self::assertNotEmpty($body);

        AssertFormDynamicRows::assert(
            $body,
            $this->formName,
            'characteristic_groups',
            'assigned_characteristic_groups'
        );
    }
}
