<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Country;

use Engine\Location\Api\Data\CountryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/country/delete';

    /**
     * @var FormKey
     */
    private $formKey;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testDelete()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            CountryInterface::COUNTRY_ID => 100,
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Country has been deleted.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/country.php
     */
    public function testDeleteWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            CountryInterface::COUNTRY_ID => 100,
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    public function testDeleteWithMissedEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    public function testDeleteWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            CountryInterface::COUNTRY_ID => -1,
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages(
            $this->contains('Country with id &quot;-1&quot; does not exist.'),
            MessageInterface::TYPE_ERROR
        );
    }
}
