<?php
namespace Engine\Location\Ui\DataProvider\FilterApplier;

use Engine\Location\Ui\DataProvider\RegionSearchResultCollection;
use Magento\Framework\Api\Filter;
use Magento\Framework\Data\Collection;
use Magento\Framework\View\Element\UiComponent\DataProvider\FilterApplierInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class StoreFilter implements FilterApplierInterface
{
    /**
     * @param Collection|RegionSearchResultCollection $collection
     * @param Filter $filter
     * @return void
     */
    public function apply(Collection $collection, Filter $filter)
    {
        $collection->addStoreData($filter->getValue());
    }
}
