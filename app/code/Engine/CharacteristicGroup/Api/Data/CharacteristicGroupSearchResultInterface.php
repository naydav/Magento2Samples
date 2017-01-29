<?php
namespace Engine\CharacteristicGroup\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 * @api
 */
interface CharacteristicGroupSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
