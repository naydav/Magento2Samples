<?php
namespace Engine\CharacteristicGroup\Test\Integration\Controller\Adminhtml\CharacteristicGroup\Save;

use Engine\CharacteristicGroup\Controller\Adminhtml\CharacteristicGroup\Save;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\CharacteristicGroupRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class CreateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-characteristic-group/characteristicGroup/save/store/%s';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CharacteristicGroupRepositoryInterface
     */
    private $characteristicGroupRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->characteristicGroupRepository = $this->_objectManager->get(
            CharacteristicGroupRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    public function testCreate()
    {
        $data = [
            CharacteristicGroupInterface::IS_ENABLED => true,
            CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
            CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
            CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0) . '/back/edit');

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $characteristicGroup = $this->getCharacteristicGroupByTitle($data[CharacteristicGroupInterface::TITLE]);
        self::assertNotEmpty($characteristicGroup);
        AssertArrayContains::assert($data, $this->hydrator->extract($characteristicGroup));

        $redirect = 'backend/engine-characteristic-group/characteristicGroup/edit/characteristic_group_id/'
            . $characteristicGroup->getCharacteristicGroupId();
        $this->assertRedirect($this->stringContains($redirect));

        self::assertEquals(
            $characteristicGroup->getCharacteristicGroupId(),
            $this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY)
        );
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::IS_ENABLED => true,
                CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
                CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
                CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup/new'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    public function testCreateAndClose()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::IS_ENABLED => true,
                CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
                CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
                CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertRedirect(
            $this->matchesRegularExpression(
                '~^((?!' . CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID . '|new).)*$~'
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Characteristic Group has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );
    }

    public function testCreateWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CharacteristicGroupInterface::IS_ENABLED => true,
                CharacteristicGroupInterface::BACKEND_TITLE => 'CharacteristicGroup-backendTitle',
                CharacteristicGroupInterface::TITLE => 'CharacteristicGroup-title',
                CharacteristicGroupInterface::DESCRIPTION => 'CharacteristicGroup-description',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-characteristic-group/characteristicGroup'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CHARACTERISTIC_GROUP_ID_KEY));
    }

    /**
     * @param string $title
     * @return CharacteristicGroupInterface
     */
    private function getCharacteristicGroupByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CharacteristicGroupInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->characteristicGroupRepository->getList($searchCriteria);
        $items = $result->getItems();
        $characteristicGroup = reset($items);
        return $characteristicGroup;
    }
}
