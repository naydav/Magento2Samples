<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City\Save;

use Engine\Location\Controller\Adminhtml\City\Save;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-location/city/save/store/%s';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CityRepositoryInterface
     */
    private $cityRepository;

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
        $this->cityRepository = $this->_objectManager->get(
            CityRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testCreate()
    {
        $data = [
            CityInterface::REGION_ID => 100,
            CityInterface::IS_ENABLED => true,
            CityInterface::POSITION => 100,
            CityInterface::TITLE => 'City-title',
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
            $this->contains('The City has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $city = $this->getCityByTitle($data[CityInterface::TITLE]);
        self::assertNotEmpty($city);
        AssertArrayContains::assert($data, $this->hydrator->extract($city));

        $redirect = 'backend/engine-location/city/edit/city_id/'
            . $city->getCityId();
        $this->assertRedirect($this->stringContains($redirect));

        self::assertEquals(
            $city->getCityId(),
            $this->registry->registry(Save::REGISTRY_CITY_ID_KEY)
        );
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::TITLE => 'City-title',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city/new'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The City has been saved.'),
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
                CityInterface::TITLE => 'City-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertRedirect(
            $this->matchesRegularExpression(
                '~^((?!' . CityInterface::CITY_ID . '|new).)*$~'
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The City has been saved.'),
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
                CityInterface::TITLE => 'City-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_CITY_ID_KEY));
    }

    /**
     * @param string $title
     * @return CityInterface
     */
    private function getCityByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CityInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->cityRepository->getList($searchCriteria);
        $items = $result->getItems();
        $city = reset($items);
        return $city;
    }
}
