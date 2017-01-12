<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\Region\Save;

use Engine\Location\Controller\Adminhtml\Region\Save;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\Framework\Test\AssertArrayContains;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Registry;
use Magento\TestFramework\TestCase\AbstractBackendController;
use Zend\Http\Request;
use Zend\Http\Response;

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
    const REQUEST_URI = 'backend/engine-location/region/save/store/%s';

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

    /**
     * @var Registry
     */
    private $registry;

    public function setUp()
    {
        parent::setUp();
        $this->formKey = $this->_objectManager->get(FormKey::class);
        $this->hydrator = $this->_objectManager->get(HydratorInterface::class);
        $this->regionRepository = $this->_objectManager->get(
            RegionRepositoryInterface::class
        );
        $this->searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        $this->registry = $this->_objectManager->get(Registry::class);
    }

    public function testCreate()
    {
        $data = [
            RegionInterface::IS_ENABLED => true,
            RegionInterface::POSITION => 100,
            RegionInterface::TITLE => 'Region-title',
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
            $this->contains('The Region has been saved.'),
            MessageInterface::TYPE_SUCCESS
        );

        $region = $this->getRegionByTitle($data[RegionInterface::TITLE]);
        self::assertNotEmpty($region);
        AssertArrayContains::assert($data, $this->hydrator->extract($region));

        $redirect = 'backend/engine-location/region/edit/region_id/'
            . $region->getRegionId();
        $this->assertRedirect($this->stringContains($redirect));

        self::assertEquals(
            $region->getregionId(),
            $this->registry->registry(Save::REGISTRY_REGION_ID_KEY)
        );
    }

    public function testCreateAndRedirectToNew()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->formKey->getFormKey(),
            'general' => [
                RegionInterface::TITLE => 'Region-title',
            ],
            'redirect_to_new' => 1,
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region/new'));
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Region has been saved.'),
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
                RegionInterface::TITLE => 'Region-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertRedirect(
            $this->matchesRegularExpression(
                '~^((?!' . RegionInterface::REGION_ID . '|new).)*$~'
            )
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->contains('The Region has been saved.'),
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
                RegionInterface::IS_ENABLED => true,
                RegionInterface::POSITION => 100,
                RegionInterface::TITLE => 'Region-title',
            ],
        ]);
        $this->dispatch(sprintf(self::REQUEST_URI, 0));

        self::assertEquals(Response::STATUS_CODE_302, $this->getResponse()->getStatusCode());
        $this->assertRedirect($this->stringContains('backend/engine-location/region'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertNull($this->registry->registry(Save::REGISTRY_REGION_ID_KEY));
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
