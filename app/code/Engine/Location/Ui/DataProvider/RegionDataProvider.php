<?php
namespace Engine\Location\Ui\DataProvider;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\CitiesByRegionList;
use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Api\RegionRepositoryInterface;
use Engine\PerStoreDataSupport\Ui\DataProvider\MetaDataBuilder;
use Engine\PerStoreDataSupport\Ui\DataProvider\SearchResultFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 * @api
 */
class RegionDataProvider extends DataProvider
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MetaDataBuilder
     */
    private $metaDataBuilder;

    /**
     * @var RegionRepositoryInterface
     */
    private $regionRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var CitiesByRegionList
     */
    private $citiesByRegionList;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param MetaDataBuilder $metaDataBuilder
     * @param RegionRepositoryInterface $regionRepository
     * @param SearchResultFactory $searchResultFactory
     * @param CitiesByRegionList $citiesByRegionList
     * @param array $meta
     * @param array $data
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
        StoreManagerInterface $storeManager,
        MetaDataBuilder $metaDataBuilder,
        RegionRepositoryInterface $regionRepository,
        SearchResultFactory $searchResultFactory,
        CitiesByRegionList $citiesByRegionList,
        array $meta = [],
        array $data = []
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
        $this->storeManager = $storeManager;
        $this->metaDataBuilder = $metaDataBuilder;
        $this->regionRepository = $regionRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->citiesByRegionList = $citiesByRegionList;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $configData = parent::getConfigData();
        $storeId = $this->storeManager->getStore()->getId();

        $configData['submit_url'] = $this->urlBuilder->getUrl(
            'engine_location/region/save',
            [
                'store' => $storeId,
            ]
        );
        $configData['validate_url'] = $this->urlBuilder->getUrl(
            'engine_location/region/validate',
            [
                'store' => $storeId,
            ]
        );
        $configData['update_url'] = $this->urlBuilder->getUrl('mui/index/render', [
            'store' => $storeId,
        ]);
        return $configData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        if ('engine_location_region_form_data_source' === $this->name) {
            $regionId = $this->request->getParam(RegionInterface::REGION_ID);
            if (null !== $regionId) {
                $fieldsMeta = $this->metaDataBuilder->build(
                    RegionInterface::class,
                    $regionId
                );
                $meta['general']['children'] = (isset($meta['general']['children']))
                    ? array_replace_recursive($meta['general']['children'], $fieldsMeta)
                    : $fieldsMeta;
            }
        }

        if ('engine_location_region_listing_data_source' === $this->name) {
            $storeId = $this->storeManager->getStore()->getId();
            $inlineEditUrl = $this->urlBuilder->getUrl('engine_location/region/inlineEdit', [
                'store' => $storeId,
            ]);
            $meta['region_columns']['arguments']['data']['config']['editorConfig']['clientConfig']
            ['saveUrl'] = $inlineEditUrl;
        }
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        if ('engine_location_region_form_data_source' === $this->name) {
            // It is need for support several fieldsets. For details see \Magento\Ui\Component\Form::getDataSourceData
            if ($data['totalRecords'] > 0) {
                $regionId = $data['items'][0][RegionInterface::REGION_ID];
                $dataForSingle[$regionId] = [
                    'general' => $data['items'][0],
                    'cities' => [
                        'assigned_cities' => $this->getAssignedCitiesData($regionId),
                    ],
                ];
                $data = $dataForSingle;
            } else {
                $data = [];
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->regionRepository->getList($searchCriteria);

        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            RegionInterface::REGION_ID
        );
        return $searchResult;
    }

    /**
     * @param int $regionId
     * @return array
     */
    private function getAssignedCitiesData($regionId)
    {
        $result = $this->citiesByRegionList->getList($regionId);
        $cities = $result->getItems();

        $assignedCities = [];
        foreach ($cities as $city) {
            $assignedCities[] = [
                CityInterface::CITY_ID => (string)$city->getCityId(),
                CityInterface::TITLE => $city->getTitle(),
                CityInterface::IS_ENABLED => (int)$city->getIsEnabled(),
            ];
        }
        return $assignedCities;
    }
}
