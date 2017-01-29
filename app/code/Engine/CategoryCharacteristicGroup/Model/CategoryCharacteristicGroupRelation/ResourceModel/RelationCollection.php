<?php
namespace Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\ResourceModel;

use Engine\CategoryCharacteristicGroup\Model\CategoryCharacteristicGroupRelation\Relation;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RelationCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Relation::class, RelationResource::class);
    }
}
