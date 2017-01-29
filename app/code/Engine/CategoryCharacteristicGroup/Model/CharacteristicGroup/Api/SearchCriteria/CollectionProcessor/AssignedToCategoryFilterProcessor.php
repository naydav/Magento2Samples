<?php
namespace Engine\CategoryCharacteristicGroup\Model\CharacteristicGroup\Api\SearchCriteria\CollectionProcessor;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssignedToCategoryFilterProcessor implements CustomFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $categoryId = (int)$filter->getValue();
        $connection = $collection->getConnection();
        $collection->getSelect()
            ->join(
                ['category_group_rel' => $connection->getTableName('engine_category_characteristic_group_relation')],
                sprintf(
                    'category_group_rel.%s = main_table.%s',
                    RelationInterface::CHARACTERISTIC_GROUP_ID,
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
                ),
                null
            )
            ->where('category_group_rel.' . RelationInterface::CATEGORY_ID . ' = ?', $categoryId)
            ->order(
                'category_group_rel.' . RelationInterface::CHARACTERISTIC_GROUP_POSITION . ' '
                . Collection::SORT_ORDER_ASC
            );
        return true;
    }
}
