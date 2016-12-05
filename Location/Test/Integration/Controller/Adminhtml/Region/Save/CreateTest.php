<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
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
    const REQUEST_URI = 'backend/location/region/save/store/%s/back/edit';

    /**
     * @var RegionInterface|null
     */
    private $region;

    public function testCreate()
    {
        $data = [
            RegionInterface::TITLE => 'region-title-create',
            RegionInterface::IS_ENABLED => 0,
            RegionInterface::POSITION => 100,
        ];

        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        self::assertNotEmpty($region);
        $this->region = $region;

        AssertArrayContains::assertArrayContains($data, $this->extractData($region));

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $region->getRegionId())
        );
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
    }

    public function tearDown()
    {
        if (null !== $this->region) {
            $this->deleteRegion($this->region);
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
     * @param RegionInterface $region
     * @return array
     */
    private function extractData(RegionInterface $region)
    {
        /** @var HydratorInterface $hydrator */
        $hydrator = $this->_objectManager->get(HydratorInterface::class);
        return $hydrator->extract($region);
    }
}
