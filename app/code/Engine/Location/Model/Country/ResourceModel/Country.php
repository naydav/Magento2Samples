<?php
declare(strict_types=1);

namespace Engine\Location\Model\Country\ResourceModel;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Magento\Model\ResourceModel\PredefinedId;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Implementation of basic operations for Country entity for specific db layer
 *
 * @author naydav <valeriy.nayda@gmail.com>
 */
class Country extends AbstractDb
{
    /**
     * Provides possibility of saving entity with predefined/pre-generated id
     */
    use PredefinedId;

    /**#@+
     * Constants related to specific db layer
     */
    const TABLE_NAME_COUNTRY = 'engine_location_country';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME_COUNTRY, CountryInterface::COUNTRY_ID);
    }
}
