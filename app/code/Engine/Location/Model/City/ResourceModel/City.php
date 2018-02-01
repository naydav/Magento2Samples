<?php
declare(strict_types=1);

namespace Engine\Location\Model\City\ResourceModel;

use Engine\Location\Api\Data\CityInterface;
use Engine\Magento\Model\ResourceModel\PredefinedId;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Implementation of basic operations for City entity for specific db layer
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class City extends AbstractDb
{
    /**
     * Provides possibility of saving entity with predefined/pre-generated id
     */
    use PredefinedId;

    /**#@+
     * Constants related to specific db layer
     */
    const TABLE_NAME_CITY = 'engine_location_city';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_CITY, CityInterface::CITY_ID);
    }
}
