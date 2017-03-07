<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroup\Api\SearchCriteria\CollectionProcessor;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class AssignedToCharacteristicFilterProcessor implements CustomFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $characteristicId = (int)$filter->getValue();
        $connection = $collection->getConnection();
        $relationTable = $connection->getTableName('engine_characteristic_group_characteristic_relation');
        $collection->getSelect()
            ->join(
                ['characteristic_group_characteristic_rel' => $relationTable],
                sprintf(
                    'characteristic_group_characteristic_rel.%s = main_table.%s',
                    RelationInterface::CHARACTERISTIC_GROUP_ID,
                    CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
                ),
                null
            )
            ->where(
                'characteristic_group_characteristic_rel.' . RelationInterface::CHARACTERISTIC_ID . ' = ?',
                $characteristicId
            );
        return true;
    }
}
