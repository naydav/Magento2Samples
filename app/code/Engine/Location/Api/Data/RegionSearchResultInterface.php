<?php
namespace Engine\Location\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RegionSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\Location\Api\Data\RegionInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\Location\Api\Data\RegionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
