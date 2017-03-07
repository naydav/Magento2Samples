<?php
namespace Engine\CharacteristicGroup\Model\Characteristic\Api\SearchCriteria\CollectionProcessor;

use Engine\Characteristic\Api\Data\CharacteristicInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection;
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
        $relationTable = $connection->getTableName('engine_characteristic_group_characteristic_relation');
        $collection->getSelect()
            ->join(
                ['characteristic_group_characteristic_rel' => $relationTable],
                sprintf(
                    'characteristic_group_characteristic_rel.%s = main_table.%s',
                    RelationInterface::CHARACTERISTIC_ID,
                    CharacteristicInterface::CHARACTERISTIC_ID
                ),
                null
            )
            ->where(
                'characteristic_group_characteristic_rel.' . RelationInterface::CHARACTERISTIC_GROUP_ID . ' = ?',
                $characteristicGroupId
            )
            ->order(
                'characteristic_group_characteristic_rel.' . RelationInterface::CHARACTERISTIC_POSITION . ' '
                . Collection::SORT_ORDER_ASC
            );
        return true;
    }
}
