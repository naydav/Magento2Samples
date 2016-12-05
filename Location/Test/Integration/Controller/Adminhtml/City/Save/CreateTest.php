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
 */
class CreateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/city/save/store/%s/back/edit';

    /**
     * @var CityInterface|null
     */
    private $city;

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
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $city = $this->getCityByTitle($data[CityInterface::TITLE]);
        self::assertNotEmpty($city);
        $this->city = $city;

        AssertArrayContains::assertArrayContains($data, $this->extractData($city));

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
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $city = $this->getCityByTitle($data[CityInterface::TITLE]);
        self::assertNotEmpty($city);
        $this->city = $city;

        self::assertNull($city->getRegionId());

        $this->assertRedirect(
            $this->stringContains('backend/location/city/edit/city_id/' . $city->getCityId())
        );
        $this->assertSessionMessages($this->contains('The City has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    public function tearDown()
    {
        if (null !== $this->city) {
            $this->deleteCity($this->city);
        }
        parent::tearDown();
    }

    /**
     * @return string
     */
    private function getFormKey()
    {
        /** @var FormKey $formKey */
        $formKey = $this->_objectManager->get(FormKey::class);
        return $formKey->getFormKey();
    }

    /**
     * @param string $title
     * @return CityInterface
     */
    private function getCityByTitle($title)
    {
        /** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
        $searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CityInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $result = $cityRepository->getList($searchCriteria);
        $items = $result->getItems();
        $city = reset($items);
        return $city;
    }

    /**
     * @param CityInterface $city
     * @return void
     */
    private function deleteCity(CityInterface $city)
    {
        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $cityRepository->deleteById($city->getCityId());
    }

    /**
     * @param CityInterface $city
     * @return array
     */
    private function extractData(CityInterface $city)
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = $this->_objectManager->get(HydratorInterface::class);
        return $hydrator->extract($city);
    }
}
