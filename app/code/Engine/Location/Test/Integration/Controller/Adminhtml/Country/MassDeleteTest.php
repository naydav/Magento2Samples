<?php
declare(strict_types=1);

namespace Engine\Location\Test\Integration\Controller\Adminhtml\Country;

use Engine\Location\Api\CountryRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class MassDeleteTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/country/massDelete';

    /**
     * @var FormKey
     */
    private $formKey;

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
        $this->countryRepository = $this->_objectManager->get(
            CountryRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/countries.php
     */
    public function testMassDelete()
    {
        $initialCountriesCount = $this->getCountriesCount();
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_location_country_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 3 Country(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(($initialCountriesCount - 3), $this->getCountriesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/countries.php
     */
    public function testMassDeleteWithWrongRequestMethod()
    {
        $initialCountriesCount = $this->getCountriesCount();
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                200,
                400,
            ],
            'namespace' => 'engine_location_country_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertEquals($initialCountriesCount, $this->getCountriesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/countries.php
     */
    public function testMassDeleteWithNotExistEntityId()
    {
        $initialCountriesCount = $this->getCountriesCount();
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                -1,
                400,
            ],
            'namespace' => 'engine_location_country_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/country'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('You deleted 2 Country(s).'),
            MessageInterface::TYPE_SUCCESS
        );
        self::assertEquals(($initialCountriesCount - 2), $this->getCountriesCount());
    }

    /**
     * @return int
     */
    private function getCountriesCount(): int
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->countryRepository->getList($searchCriteria);
        return $result->getTotalCount();
    }
}
