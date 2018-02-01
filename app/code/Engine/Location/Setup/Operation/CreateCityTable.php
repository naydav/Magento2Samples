<?php
declare(strict_types=1);

namespace Engine\Location\Setup\Operation;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Model\City\ResourceModel\City as CityResourceModel;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CreateCityTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $cityTable = $this->createCityTable($setup);

        $setup->getConnection()->createTable($cityTable);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCityTable(SchemaSetupInterface $setup): Table
    {
        $cityTable = $setup->getTable(CityResourceModel::TABLE_NAME_CITY);

        return $setup->getConnection()->newTable(
            $cityTable
        )->addColumn(
            CityInterface::CITY_ID,
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_IDENTITY => true,
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_PRIMARY => true,
            ],
            'City Id'
        )->addColumn(
            CityInterface::REGION_ID,
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
            ],
            'Region'
        )->addColumn(
            CityInterface::ENABLED,
            Table::TYPE_SMALLINT,
            1,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 1,
            ],
            'Is Enabled'
        )->addColumn(
            CityInterface::POSITION,
            Table::TYPE_SMALLINT,
            3,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 0,
            ],
            'Position'
        )->addColumn(
            CityInterface::NAME,
            Table::TYPE_TEXT,
            255,
            [Table::OPTION_NULLABLE => false],
            'Name'
        )->addIndex(
            $setup->getIdxName($cityTable, [
                CityInterface::ENABLED,
                CityInterface::POSITION,
             ]),
            [CityInterface::ENABLED, CityInterface::POSITION]
        );
    }
}
