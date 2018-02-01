<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Country\Save;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Api\CountryRepositoryInterface;
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
    const REQUEST_URI = 'backend/engine-location/country/save';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->countryRepository = $this->_objectManager->get(
            CountryRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    public function testCreate()
    {
        $data = [
            CountryInterface::ENABLED => true,
            CountryInterface::POSITION => 100,
            CountryInterface::NAME => 'Country-name',
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
            $this->contains('The Country has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $country = $this->getCountryByName($data[CountryInterface::NAME]);
        self::assertNotEmpty($country);
        AssertArrayContains::assert($data, $this->hydrator->extract($country));

        $redirect = 'backend/engine-location/country/edit/country_id/'
            . $country->getCountryId();
        $this->assertRedirect($this->stringContains($redirect));
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                CountryInterface::ENABLED => true,
                CountryInterface::POSITION => 100,
                CountryInterface::NAME => 'Country-name',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country/new'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Country has been saved.'),
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
                CountryInterface::ENABLED => true,
                CountryInterface::POSITION => 100,
                CountryInterface::NAME => 'Country-name',
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertRedirect(
            $this->matchesRegularExpression(
                '~^((?!' . CountryInterface::COUNTRY_ID . '|new).)*$~'
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Country has been saved.'),
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
                CountryInterface::ENABLED => true,
                CountryInterface::POSITION => 100,
                CountryInterface::NAME => 'Country-name',
            ],
        ]);
        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
    }

    /**
     * @param string $name
     * @return CountryInterface
     */
    private function getCountryByName(string $name): CountryInterface
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CountryInterface::NAME, $name);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->countryRepository->getList($searchCriteria);
        $items = $result->getItems();
        $country = reset($items);
        return $country;
    }
}
