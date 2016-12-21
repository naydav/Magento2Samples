<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City\Save;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;

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
    const REQUEST_URI = 'backend/location/city/save/store/%s/back/edit';

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
        $this->cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testCreate()
    {
        $data = [
            CityInterface::REGION_ID => 100,
            CityInterface::TITLE => 'city-title-create',
            CityInterface::IS_ENABLED => 0,
            CityInterface::POSITION => 100,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $city = $this->getCityByTitle($data[CityInterface::TITLE]);

        self::assertNotEmpty($city);
        AssertArrayContains::assertArrayContains($data, $this->hydrator->extract($city));

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $city->getCityId())
        );
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    public function testCreateWithoutRegion()
    {
        $data = [
            CityInterface::TITLE => 'city-title-create',
            CityInterface::IS_ENABLED => 0,
            CityInterface::POSITION => 100,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $city = $this->getCityByTitle($data[CityInterface::TITLE]);

        self::assertNotEmpty($city);
        self::assertNull($city->getRegionId());

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $city->getCityId())
        );
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);
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
