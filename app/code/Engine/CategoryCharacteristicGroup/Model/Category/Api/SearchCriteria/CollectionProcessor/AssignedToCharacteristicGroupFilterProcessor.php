<?php
namespace Engine\CategoryCharacteristicGroup\Model\Category\Api\SearchCriteria\CollectionProcessor;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssignedToCharacteristicGroupFilterProcessor implements CustomFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $characteristicGroupId = (int)$filter->getValue();
        $connection = $collection->getConnection();
        $collection->getSelect()
            ->join(
                ['category_group_rel' => $connection->getTableName('engine_category_characteristic_group_relation')],
                sprintf(
                    'category_group_rel.%s = main_table.%s',
                    RelationInterface::CATEGORY_ID,
                    CategoryInterface::CATEGORY_ID
                ),
                null
            )
            ->where(
                'category_group_rel.' . RelationInterface::CHARACTERISTIC_GROUP_ID . ' = ?',
                $characteristicGroupId
            );
        return true;
    }
}
