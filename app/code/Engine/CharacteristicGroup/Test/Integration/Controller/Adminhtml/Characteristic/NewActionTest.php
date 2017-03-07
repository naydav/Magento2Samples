<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\Characteristic;

use Engine\Backend\Test\AssertFormDynamicRows;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class NewActionTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic/characteristic/new';

    /**
     * @var string
     */
    private $formName = 'engine_characteristic_form';

    public function testEdit()
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
