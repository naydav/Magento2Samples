<?php
namespace Engine\CategoryCharacteristicGroup\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface RelationSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
