<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

use Engine\Location\Api\CityRepositoryInterface;
use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Location\Model\City\CitiesByRegionList;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 */
class CityManagementTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/region/save/store/%s/back/edit';

    /**
     * @var RegionInterface|null
     */
    private $region;

    /**
     * @var CityInterface[]
     */
    private $cities;

    public function testCreateWithCitiesCreating()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-create',
        ];
        $assignedCities = [
            [
                CityInterface::TITLE => 'city-1',
                CityInterface::IS_ENABLED => 1,
            ],
            [
                CityInterface::TITLE => 'city-2',
                CityInterface::IS_ENABLED => 0,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        $this->region = $region;
        $cities = $this->getAssignedCities($region->getRegionId());
        $this->cities = $cities;

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals($assignedCities[0][CityInterface::TITLE], $cities[0]->getTitle());
        self::assertEquals($assignedCities[0][CityInterface::IS_ENABLED], $cities[0]->getIsEnabled());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals($assignedCities[1][CityInterface::TITLE], $cities[1]->getTitle());
        self::assertEquals($assignedCities[1][CityInterface::IS_ENABLED], $cities[1]->getIsEnabled());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testCreateWithCurrentCitiesSelecting()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-create',
        ];
        $assignedCities = [
            [
                'id' => 100,
            ],
            [
                'id' => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        $this->region = $region;
        $cities = $this->getAssignedCities($region->getRegionId());
        $this->cities = $cities;

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals(100, $cities[0]->getCityId());
        self::assertEquals('city-3', $cities[0]->getTitle());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals(200, $cities[1]->getCityId());
        self::assertEquals('city-2', $cities[1]->getTitle());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     */
    public function testUpdateWithCitiesCreating()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::TITLE => 'city-1',
                CityInterface::IS_ENABLED => 1,
            ],
            [
                CityInterface::TITLE => 'city-2',
                CityInterface::IS_ENABLED => 0,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle('title-0');
        $cities = $this->getAssignedCities($region->getRegionId());
        $this->cities = $cities;

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals($assignedCities[0][CityInterface::TITLE], $cities[0]->getTitle());
        self::assertEquals($assignedCities[0][CityInterface::IS_ENABLED], $cities[0]->getIsEnabled());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals($assignedCities[1][CityInterface::TITLE], $cities[1]->getTitle());
        self::assertEquals($assignedCities[1][CityInterface::IS_ENABLED], $cities[1]->getIsEnabled());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testUpdateWithCurrentCitiesSelecting()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                'id' => 100,
            ],
            [
                'id' => 200,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle('title-0');
        $cities = $this->getAssignedCities($region->getRegionId());

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals(100, $cities[0]->getCityId());
        self::assertEquals('city-3', $cities[0]->getTitle());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals(200, $cities[1]->getCityId());
        self::assertEquals('city-2', $cities[1]->getTitle());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testUpdateWithCitiesReplacing()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                'id' => 300,
            ],
            [
                'id' => 400,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle('title-0');
        $cities = $this->getAssignedCities($region->getRegionId());

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals(300, $cities[0]->getCityId());
        self::assertEquals('city-2', $cities[0]->getTitle());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals(400, $cities[1]->getCityId());
        self::assertEquals('city-1', $cities[1]->getTitle());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city.php
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testCitiesDataUpdating()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                'id' => 100,
                CityInterface::TITLE => 'city-1001',
                CityInterface::IS_ENABLED => 0,
            ],
            [
                'id' => 200,
                CityInterface::TITLE => 'city-1002',
                CityInterface::IS_ENABLED => 0,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle('title-0');
        $cities = $this->getAssignedCities($region->getRegionId());

        self::assertNotEmpty($region);
        self::assertCount(2, $cities);

        self::assertEquals(100, $cities[0]->getCityId());
        self::assertEquals('city-1001', $cities[0]->getTitle());
        self::assertFalse($cities[0]->getIsEnabled());
        self::assertEquals(10, $cities[0]->getPosition());

        self::assertEquals(200, $cities[1]->getCityId());
        self::assertEquals('city-1002', $cities[1]->getTitle());
        self::assertFalse($cities[1]->getIsEnabled());
        self::assertEquals(20, $cities[1]->getPosition());

        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    public function tearDown()
    {
        if (null !== $this->region) {
            $this->deleteRegion($this->region);
        }
        if (count($this->cities) > 0) {
            $this->deleteCities($this->cities);
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
     * @return RegionInterface
     */
    private function getRegionByTitle($title)
    {
        /** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
        $searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(RegionInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var RegionRepositoryInterface $regionRepository */
        $regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $result = $regionRepository->getList($searchCriteria);
        $items = $result->getItems();
        $region = reset($items);
        return $region;
    }

    /**
     * @param RegionInterface $region
     * @return void
     */
    private function deleteRegion(RegionInterface $region)
    {
        /** @var RegionRepositoryInterface $regionRepository */
        $regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $regionRepository->deleteById($region->getRegionId());
    }

    /**
     * @param int $regionId
     * @return CityInterface[]
     */
    private function getAssignedCities($regionId)
    {
        /** @var CitiesByRegionList $citiesByRegionList */
        $citiesByRegionList = $this->_objectManager->get(CitiesByRegionList::class);
        return $citiesByRegionList->getList($regionId)->getItems();
    }

    /**
     * @param CityInterface[] $cities
     * @return void
     */
    private function deleteCities(array $cities)
    {
        foreach ($cities as $city) {
            /** @var CityRepositoryInterface $cityRepository */
            $cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
            $cityRepository->deleteById($city->getCityId());
        }
    }
}
