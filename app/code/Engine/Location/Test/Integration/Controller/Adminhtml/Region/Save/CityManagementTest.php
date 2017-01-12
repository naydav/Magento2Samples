<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

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
use Zend\Http\Response;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 */
class CityManagementTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/engine-location/region/save/store/%s/back/edit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var CitiesByRegionList
     */
    private $citiesByRegionList;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->citiesByRegionList = $this->_objectManager->get(CitiesByRegionList::class);
    }

    public function testCreateWithCitiesCreating()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-create',
        ];
        $assignedCities = [
            [
                CityInterface::TITLE => 'City-title-1',
                CityInterface::IS_ENABLED => 1,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::TITLE => 'City-title-2',
                CityInterface::IS_ENABLED => 0,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals($assignedCities[1][CityInterface::TITLE], $cities[0]->getTitle());
        self::assertEquals($assignedCities[1][CityInterface::IS_ENABLED], $cities[0]->getIsEnabled());

        self::assertEquals($assignedCities[0][CityInterface::TITLE], $cities[1]->getTitle());
        self::assertEquals($assignedCities[0][CityInterface::IS_ENABLED], $cities[1]->getIsEnabled());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testCreateWithCurrentCitiesSelecting()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-create',
        ];
        $assignedCities = [
            [
                CityInterface::CITY_ID => 100,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::CITY_ID => 200,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals(200, $cities[0]->getCityId());
        self::assertEquals('City-title-2', $cities[0]->getTitle());

        self::assertEquals(100, $cities[1]->getCityId());
        self::assertEquals('City-title-3', $cities[1]->getTitle());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_id_100.php
     */
    public function testUpdateWithCitiesCreating()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::TITLE => 'City-title-1',
                CityInterface::IS_ENABLED => 1,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::TITLE => 'City-title-2',
                CityInterface::IS_ENABLED => 0,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle('Region-title-100');
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals($assignedCities[1][CityInterface::TITLE], $cities[0]->getTitle());
        self::assertEquals($assignedCities[1][CityInterface::IS_ENABLED], $cities[0]->getIsEnabled());

        self::assertEquals($assignedCities[0][CityInterface::TITLE], $cities[1]->getTitle());
        self::assertEquals($assignedCities[0][CityInterface::IS_ENABLED], $cities[1]->getIsEnabled());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testUpdateWithCurrentCitiesSelecting()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::CITY_ID => 100,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::CITY_ID => 200,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle('Region-title-100');
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals(100, $cities[1]->getCityId());
        self::assertEquals('City-title-3', $cities[1]->getTitle());

        self::assertEquals(200, $cities[0]->getCityId());
        self::assertEquals('City-title-2', $cities[0]->getTitle());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testUpdateWithCitiesReplacing()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::CITY_ID => 300,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::CITY_ID => 400,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle('Region-title-100');
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals(400, $cities[0]->getCityId());
        self::assertEquals('City-title-1', $cities[0]->getTitle());

        self::assertEquals(300, $cities[1]->getCityId());
        self::assertEquals('City-title-2', $cities[1]->getTitle());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testCitiesDataUpdating()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::CITY_ID => 100,
                CityInterface::TITLE => 'City-title-1001',
                CityInterface::IS_ENABLED => 0,
                CityInterface::POSITION => 2,
            ],
            [
                CityInterface::CITY_ID => 200,
                CityInterface::TITLE => 'City-title-1002',
                CityInterface::IS_ENABLED => 0,
                CityInterface::POSITION => 1,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);

        $region = $this->getRegionByTitle('Region-title-100');
        self::assertNotEmpty($region);

        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals(200, $cities[0]->getCityId());
        self::assertEquals('City-title-1002', $cities[0]->getTitle());
        self::assertFalse($cities[0]->getIsEnabled());

        self::assertEquals(100, $cities[1]->getCityId());
        self::assertEquals('City-title-1001', $cities[1]->getTitle());
        self::assertFalse($cities[1]->getIsEnabled());

        $this->assertRedirect(
            $this->stringContains('backend/engine-location/region/edit/region_id/' . $region->getRegionId())
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope.php
     */
    public function testUpdateWithWrongRequestMethod()
    {
        $regionId = 100;
        $data = [
            RegionInterface::REGION_ID => $regionId,
        ];
        $assignedCities = [
            [
                CityInterface::CITY_ID => 300,
                CityInterface::POSITION => 2,
            ],
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setQueryValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
            'cities' => [
                'assigned_cities' => $assignedCities,
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);

        $region = $this->getRegionByTitle('Region-title-100');
        $cities = $this->getAssignedCities($region->getRegionId());
        self::assertCount(2, $cities);

        self::assertEquals(200, $cities[0]->getCityId());
        self::assertEquals('City-title-2', $cities[0]->getTitle());

        self::assertEquals(100, $cities[1]->getCityId());
        self::assertEquals('City-title-3', $cities[1]->getTitle());
    }

    /**
     * @param string $title
     * @return RegionInterface
     */
    private function getRegionByTitle($title)
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(RegionInterface::TITLE, $title);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->regionRepository->getList($searchCriteria);
        $items = $result->getItems();
        $region = reset($items);
        return $region;
    }

    /**
     * @param int $regionId
     * @return CityInterface[]
     */
    private function getAssignedCities($regionId)
    {
        return $this->citiesByRegionList->getList($regionId)->getItems();
    }
}
