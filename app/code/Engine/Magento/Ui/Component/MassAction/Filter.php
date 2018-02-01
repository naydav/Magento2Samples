<?php
declare(strict_types=1);

namespace Engine\Magento\Ui\Component\MassAction;

use Magento\Ui\Component\MassAction\Filter as BaseFilter;
use Magento\Framework\Api\Search\DocumentInterface;

/**
 * Temporary solution
 * @todo Need to remove after fixing the issue
 * @see https://github.com/magento/magento2/issues/10988
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
     * Get ids from search filter
     *
     * @return array
     */
    public function getIds()
    {
        $this->filter->applySelectionOnTargetProvider();
        $component = $this->filter->getComponent();
        $dataProvider = $component->getContext()->getDataProvider();
        $searchResult = $dataProvider->getSearchResult();

        return array_map(function (DocumentInterface $item) {
            return $item->getId();
        }, $searchResult->getItems());
    }
}
