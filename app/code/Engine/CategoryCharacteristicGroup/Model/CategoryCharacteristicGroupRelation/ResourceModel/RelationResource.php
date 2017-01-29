<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\ResourceModel;

use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RelationResource extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            'engine_category_characteristic_group_relation',
            RelationInterface::RELATION_ID
        );
    }
}
