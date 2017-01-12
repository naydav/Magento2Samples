<?php
namespace Engine\Category\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CategorySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\Category\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\Category\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
