<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\Backend\Api\StoreContextInterface;
use Engine\Backend\Api\Ui\DataProvider\StoreMetaModifierInterface;
use Engine\Location\Model\Region\RegionPerStoreLoader;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\Store;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionDataProvider extends DataProvider
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RegionPerStoreLoader
     */
    private $regionPerStoreLoader;

    /**
     * @var array
     */
    private $perScopeFields;

    /**
     * @var StoreContextInterface
     */
    private $storeContext;

    /**
     * @var StoreMetaModifierInterface
     */
    private $storeMetaModifier;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param UrlInterface $urlBuilder
     * @param RegionPerStoreLoader $regionPerStoreLoader
     * @param Registry $registry
     * @param StoreContextInterface $storeContext
     * @param StoreMetaModifierInterface $storeMetaModifier
     * @param array $meta
     * @param array $data
     * @param array $perScopeFields
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        UrlInterface $urlBuilder,
        RegionPerStoreLoader $regionPerStoreLoader,
        Registry $registry,
        StoreContextInterface $storeContext,
        StoreMetaModifierInterface $storeMetaModifier,
        array $meta = [],
        array $data = [],
        array $perScopeFields = ['title']
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->urlBuilder = $urlBuilder;
        $this->regionPerStoreLoader = $regionPerStoreLoader;
        $this->storeContext = $storeContext;
        $this->storeMetaModifier = $storeMetaModifier;
        $this->perScopeFields = $perScopeFields;
    }

    /**
     * Returns search criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->addStoreFilter();
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
        }
        return $this->searchCriteria;
    }

    /**
     * Apply store filter
     */
    private function addStoreFilter()
    {
        $storeId = $this->storeContext->getCurrentStore()->getId();

        $filter = $this->filterBuilder
            ->setField('store')
            ->setValue($storeId)
            ->setConditionType('store')
            ->create();
        $this->searchCriteriaBuilder->addFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $configData = parent::getConfigData();
        $storeId = $this->storeContext->getCurrentStore()->getId();

        $configData['submit_url'] = $this->urlBuilder->getUrl('*/*/save', [
            'store' => $storeId,
        ]);
        return $configData;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $storeId = $this->storeContext->getCurrentStore()->getId();

        if (Store::DEFAULT_STORE_ID !== (int)$storeId) {
            $regionId = $this->request->getParam('region_id');
            if (null !== $regionId) {
                $regionInGlobalScope = $this->regionPerStoreLoader->load($regionId, Store::DEFAULT_STORE_ID);
                $regionInCurrentScope = $this->regionPerStoreLoader->load($regionId, $storeId);

                $meta = $this->storeMetaModifier->modify(
                    $meta,
                    $this->perScopeFields,
                    $regionInGlobalScope,
                    $regionInCurrentScope
                );
            }
        }
        return $meta;
    }
}
