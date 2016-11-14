<?php
namespace Engine\Location\Setup;

use Engine\Location\Api\Data\CityInterface;
use Engine\Location\Api\Data\RegionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author  naydav <valeriy.nayda@gmail.com>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $setup->getConnection()->createTable($this->createRegionTable($setup));
        $setup->getConnection()->createTable($this->createRegionStoreTable($setup));
        $setup->getConnection()->createTable($this->createCityTable($setup));
        $setup->getConnection()->createTable($this->createCityStoreTable($setup));
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createRegionTable(SchemaSetupInterface $setup)
    {
        $regionTable = $setup->getTable('engine_location_region');

        return $setup->getConnection()->newTable(
            $regionTable
        )->addColumn(
            RegionInterface::REGION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Region Id'
        )->addColumn(
            RegionInterface::IS_ENABLED,
            Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is enabled'
        )->addColumn(
            RegionInterface::POSITION,
            Table::TYPE_SMALLINT,
            3,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Position'
        )->addIndex(
            $setup->getIdxName($regionTable, [RegionInterface::IS_ENABLED, RegionInterface::POSITION]),
            [RegionInterface::IS_ENABLED, RegionInterface::POSITION]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createRegionStoreTable(SchemaSetupInterface $setup)
    {
        $regionStoreTable = $setup->getTable('engine_location_region_store');
        $regionTable = $setup->getTable('engine_location_region');
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()->newTable(
            $regionStoreTable
        )->addColumn(
            RegionInterface::REGION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Translatable id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store id'
        )->addColumn(
            RegionInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addIndex(
            'idx_primary',
            ['store_id', RegionInterface::REGION_ID],
            ['type' => AdapterInterface::INDEX_TYPE_PRIMARY]
        )->addIndex(
            $setup->getIdxName(
                $regionStoreTable,
                [RegionInterface::REGION_ID, 'store_id']
            ),
            [RegionInterface::REGION_ID, 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(
                $regionStoreTable,
                RegionInterface::REGION_ID,
                $regionTable,
                RegionInterface::REGION_ID
            ),
            RegionInterface::REGION_ID,
            $regionTable,
            RegionInterface::REGION_ID,
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $regionStoreTable,
                'store_id',
                $storeTable,
                'store_id'
            ),
            'store_id',
            $storeTable,
            'store_id',
            AdapterInterface::FK_ACTION_CASCADE
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCityTable(SchemaSetupInterface $setup)
    {
        $cityTable = $setup->getTable('engine_location_city');
        $regionTable = $setup->getTable('engine_location_region');

        return $setup->getConnection()->newTable(
            $cityTable
        )->addColumn(
            CityInterface::CITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'City Id'
        )->addColumn(
            CityInterface::REGION_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Region Id'
        )->addColumn(
            CityInterface::IS_ENABLED,
            Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is enabled'
        )->addColumn(
            CityInterface::POSITION,
            Table::TYPE_SMALLINT,
            3,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Position'
        )->addIndex(
            $setup->getIdxName(
                $cityTable,
                [CityInterface::REGION_ID, CityInterface::IS_ENABLED, CityInterface::POSITION]
            ),
            [CityInterface::REGION_ID, CityInterface::IS_ENABLED, CityInterface::POSITION]
        )->addForeignKey(
            $setup->getFkName(
                $cityTable,
                CityInterface::REGION_ID,
                $regionTable,
                RegionInterface::REGION_ID
            ),
            CityInterface::REGION_ID,
            $regionTable,
            RegionInterface::REGION_ID,
            AdapterInterface::FK_ACTION_CASCADE
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCityStoreTable(SchemaSetupInterface $setup)
    {
        $cityStoreTable = $setup->getTable('engine_location_city_store');
        $cityTable = $setup->getTable('engine_location_city');
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()->newTable(
            $cityStoreTable
        )->addColumn(
            CityInterface::CITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Translatable id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store id'
        )->addColumn(
            CityInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addIndex(
            'idx_primary',
            ['store_id', CityInterface::CITY_ID],
            ['type' => AdapterInterface::INDEX_TYPE_PRIMARY]
        )->addIndex(
            $setup->getIdxName(
                $cityStoreTable,
                [CityInterface::CITY_ID, 'store_id']
            ),
            [CityInterface::CITY_ID, 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(
                $cityStoreTable,
                CityInterface::CITY_ID,
                $cityTable,
                CityInterface::CITY_ID
            ),
            CityInterface::CITY_ID,
            $cityTable,
            CityInterface::CITY_ID,
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $cityStoreTable,
                'store_id',
                $storeTable,
                'store_id'
            ),
            'store_id',
            $storeTable,
            'store_id',
            AdapterInterface::FK_ACTION_CASCADE
        );
    }
}
