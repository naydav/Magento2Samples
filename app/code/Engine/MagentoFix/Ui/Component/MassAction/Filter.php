<?php
namespace Engine\MagentoFix\Ui\Component\MassAction;

use Magento\Ui\Component\MassAction\Filter as BaseFilter;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class Filter
{
    /**
     * @var BaseFilter
     */
    private $filter;

    /**
     * @param BaseFilter $filter
     */
    public function __construct(
        BaseFilter $filter
    ) {
        $this->filter = $filter;
    }

    /**
     * @return array
     */
    public function getIds()
    {
        $this->filter->applySelectionOnTargetProvider();
        $component = $this->filter->getComponent();
        $dataProvider = $component->getContext()->getDataProvider();
        $searchResult = $dataProvider->getSearchResult();

        $ids = [];
        foreach ($searchResult->getItems() as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }
}
