<?php
namespace Engine\Location\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CitySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\Location\Api\Data\CityInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\Location\Api\Data\CityInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
