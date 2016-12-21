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
 * @magentoDbIsolation enabled
 */
class CreateTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/region/save/store/%s/back/edit';

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

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
            'form_key' => $this->formKey->getFormKey(),
            'general' => $data,
        ]);

        $uri = sprintf(self::REQUEST_URI, 0);
        $this->dispatch($uri);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);

        self::assertNotEmpty($region);
        AssertArrayContains::assertArrayContains($data, $this->hydrator->extract($region));

        $this->assertRedirect(
            $this->stringContains('backend/location/region/edit/region_id/' . $region->getRegionId())
        );
        $this->assertSessionMessages($this->contains('The Region has been saved.'), MessageInterface::TYPE_SUCCESS);
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
}
