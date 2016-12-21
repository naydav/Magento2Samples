<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
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
class MassStatusTest extends AbstractBackendController
{
    /**
     * Request uri
     */
    const REQUEST_URI = 'backend/location/region/massStatus';

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

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->regionRepository = $this->_objectManager->get(RegionRepositoryInterface::class);
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope.php
     */
    public function testMassStatus()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
            ],
            'namespace' => 'region_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
        $this->assertSessionMessages($this->contains('You updated 1 region(s).'), MessageInterface::TYPE_SUCCESS);
        self::assertEquals(1, $this->getEnabledRegionsCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope.php
     */
    public function testMassStatusWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
            ],
            'namespace' => 'region_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertEquals(2, $this->getEnabledRegionsCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/region/region_list_global_scope.php
     */
    public function testMassStatusWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'selected' => [
                100,
                -1,
            ],
            'namespace' => 'region_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/region/index'));
        $this->assertSessionMessages($this->contains('You updated 1 region(s).'), MessageInterface::TYPE_SUCCESS);
        self::assertEquals(1, $this->getEnabledRegionsCount());
    }

    /**
     * @return int
     */
    private function getEnabledRegionsCount()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(RegionInterface::IS_ENABLED, true);
        $searchCriteria = $searchCriteriaBuilder->create();

        $result = $this->regionRepository->getList($searchCriteria);
        return $result->getTotalCount();
    }
}
