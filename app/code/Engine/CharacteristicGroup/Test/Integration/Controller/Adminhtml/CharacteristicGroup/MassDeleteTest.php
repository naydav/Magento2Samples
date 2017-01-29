<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup;

use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/massDelete';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list_global_scope.php
     */
    public function testMassDelete()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_characteristic_group_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 3 Characteristic Group(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(1, $this->getCharacteristicGroupsCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list_global_scope.php
     */
    public function testMassDeleteWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_characteristic_group_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertEquals(4, $this->getCharacteristicGroupsCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/CharacteristicGroup/Test/_files/characteristic_group/characteristic_group_list_global_scope.php
     */
    public function testMassDeleteWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                -1,
                400,
            ],
            'namespace' => 'engine_characteristic_group_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 2 Characteristic Group(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(2, $this->getCharacteristicGroupsCount());
    }

    /**
     * @return int
     */
    private function getCharacteristicGroupsCount()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->characteristicGroupRepository->getList($searchCriteria);
        return $result->getTotalCount();
    }
}
