<?php
namespace Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\ResourceModel;

use Engine\CharacteristicGroup\Model\CharacteristicGroupCharacteristicRelation\Relation;
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
