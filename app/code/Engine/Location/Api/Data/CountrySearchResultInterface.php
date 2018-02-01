<?php
declare(strict_types=1);

namespace Engine\Location\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Search results of Repository::getList method
 *
 * Used fully qualified namespaces in annotations for proper work of WebApi request parser
 *
 * @api
 * @author naydav <valeriy.nayda@gmail.com>
 */
interface CountrySearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Engine\Location\Api\Data\CountryInterface[]
     */
    public function getItems();

    /**
     * @param \Engine\Location\Api\Data\CountryInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
