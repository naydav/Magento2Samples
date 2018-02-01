<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\City\Save;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * If test has not fixture then magentoDbIsolation will be disabled
 * @magentoDbIsolation enabled
 */
class CreateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/city/save';

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

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->cityRepository = $this->_objectManager->get(
            CityRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    public function testCreate()
    {
        $data = [
            CityInterface::REGION_ID => 100,
            CityInterface::ENABLED => true,
            CityInterface::POSITION => 100,
            CityInterface::NAME => 'City-name',
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

        $city = $this->getCityByName($data[CityInterface::NAME]);
        self::assertNotEmpty($city);
        AssertArrayContains::assert($data, $this->hydrator->extract($city));

        $redirect = 'backend/engine-location/city/edit/city_id/'
            . $city->getCityId();
        $this->assertRedirect($this->stringContains($redirect));
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CityInterface::REGION_ID => 100,
                CityInterface::ENABLED => true,
                CityInterface::POSITION => 100,
                CityInterface::NAME => 'City-name',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(self::REQUEST_URI);

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
                CityInterface::REGION_ID => 100,
                CityInterface::ENABLED => true,
                CityInterface::POSITION => 100,
                CityInterface::NAME => 'City-name',
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

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
                CityInterface::REGION_ID => 100,
                CityInterface::ENABLED => true,
                CityInterface::POSITION => 100,
                CityInterface::NAME => 'City-name',
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/city'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @param string $name
     * @return CityInterface
     */
    private function getCityByName(string $name): CityInterface
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CityInterface::NAME, $name);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->cityRepository->getList($searchCriteria);
        $items = $result->getItems();
        $city = reset($items);
        return $city;
    }
}
