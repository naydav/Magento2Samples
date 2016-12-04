<?php
namespace Engine\Location\Test\Integration\Controller\Adminhtml\City;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\CityRepositoryInterface;
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
    const REQUEST_URI = 'backend/location/city/massStatus';

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testMassStatus()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'selected' => [
                100,
            ],
            'namespace' => 'city_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages($this->contains('You updated 1 city(s).'), MessageInterface::TYPE_SUCCESS);
        self::assertEquals(1, $this->getEnabledCitiesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testMassStatusWithWrongRequestMethod()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_GET);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'selected' => [
                100,
            ],
            'namespace' => 'city_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages($this->contains('Wrong request.'), MessageInterface::TYPE_ERROR);
        self::assertEquals(2, $this->getEnabledCitiesCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Engine/Location/Test/_files/city/city_list_global_scope_data.php
     */
    public function testMassStatusWithNotExistEntityId()
    {
        $request = $this->getRequest();
        $request->setMethod(Request::METHOD_POST);
        $request->setPostValue([
            'form_key' => $this->getFormKey(),
            'selected' => [
                100,
                -1,
            ],
            'namespace' => 'city_listing',
        ]);

        $this->dispatch(self::REQUEST_URI);

        $this->assertRedirect($this->stringContains('backend/location/city/index'));
        $this->assertSessionMessages($this->contains('You updated 1 city(s).'), MessageInterface::TYPE_SUCCESS);
        self::assertEquals(1, $this->getEnabledCitiesCount());
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
     * @return int
     */
    private function getEnabledCitiesCount()
    {
        /** @var SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory */
        $searchCriteriaBuilderFactory = $this->_objectManager->get(SearchCriteriaBuilderFactory::class);
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = $searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter(CityInterface::IS_ENABLED, true);
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var CityRepositoryInterface $cityRepository */
        $cityRepository = $this->_objectManager->get(CityRepositoryInterface::class);
        $result = $cityRepository->getList($searchCriteria);
        return $result->getTotalCount();
    }
}
