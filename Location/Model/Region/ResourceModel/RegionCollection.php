<?php
namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Model\Region\Region;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class RegionCollection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Region::class, RegionResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdFieldName()
    {
        return $this->getResource()->getIdFieldName();
    }

    /**
     * @param int $storeId
     * @return void
     */
    public function addStoreData($storeId)
    {
        $regionStoreTableAlias = 'store_table';
        if (!isset($this->_joinedTables[$regionStoreTableAlias])) {
            $this->getSelect()->joinLeft(
                [$regionStoreTableAlias => $this->getTable('engine_location_region_store')],
                'store_table.region_id = main_table.region_id AND store_table.store_id = ' . (int)$storeId
            );
            $this->_joinedTables[$regionStoreTableAlias] = true;
        }
    }
}
