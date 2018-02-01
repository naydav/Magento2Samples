<?php
declare(strict_types=1);

namespace Engine\Location\Model\Region\ResourceModel;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Magento\Model\ResourceModel\PredefinedId;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Implementation of basic operations for Region entity for specific db layer
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Region extends AbstractDb
{
    /**
     * Provides possibility of saving entity with predefined/pre-generated id
     */
    use PredefinedId;

    /**#@+
     * Constants related to specific db layer
     */
    const TABLE_NAME_REGION = 'engine_location_region';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_REGION, RegionInterface::REGION_ID);
    }
}
