<?php
declare(strict_types=1);

namespace Engine\Location\Setup\Operation;

use Engine\Location\Api\Data\RegionInterface;
use Engine\Location\Model\Region\ResourceModel\Region as RegionResourceModel;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CreateRegionTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $regionTable = $this->createRegionTable($setup);

        $setup->getConnection()->createTable($regionTable);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createRegionTable(SchemaSetupInterface $setup): Table
    {
        $regionTable = $setup->getTable(RegionResourceModel::TABLE_NAME_REGION);

        return $setup->getConnection()->newTable(
            $regionTable
        )->addColumn(
            RegionInterface::REGION_ID,
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_IDENTITY => true,
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_PRIMARY => true,
            ],
            'Region Id'
        )->addColumn(
            RegionInterface::COUNTRY_ID,
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
            ],
            'Country'
        )->addColumn(
            RegionInterface::ENABLED,
            Table::TYPE_SMALLINT,
            1,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 1,
            ],
            'Is Enabled'
        )->addColumn(
            RegionInterface::POSITION,
            Table::TYPE_SMALLINT,
            3,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 0,
            ],
            'Position'
        )->addColumn(
            RegionInterface::NAME,
            Table::TYPE_TEXT,
            255,
            [Table::OPTION_NULLABLE => false],
            'Name'
        )->addIndex(
            $setup->getIdxName($regionTable, [
                RegionInterface::ENABLED,
                RegionInterface::POSITION,
             ]),
            [RegionInterface::ENABLED, RegionInterface::POSITION]
        );
    }
}
