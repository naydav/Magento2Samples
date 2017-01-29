<?php
namespace Engine\CharacteristicGroup\Setup;

use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
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
        $setup->getConnection()->createTable($this->createCharacteristicGroupTable($setup));
        $setup->getConnection()->createTable($this->createCharacteristicGroupStoreTable($setup));
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCharacteristicGroupTable(SchemaSetupInterface $setup)
    {
        $characteristicGroupTable = $setup->getTable('engine_characteristic_group');

        return $setup->getConnection()->newTable(
            $characteristicGroupTable
        )->addColumn(
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Characteristic Group Id'
        )->addColumn(
            CharacteristicGroupInterface::IS_ENABLED,
            Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => 1],
            'Is Enabled'
        )->addColumn(
            CharacteristicGroupInterface::BACKEND_TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Backend Title'
        )->addIndex(
            $setup->getIdxName($characteristicGroupTable, [
                CharacteristicGroupInterface::IS_ENABLED,
            ]),
            [CharacteristicGroupInterface::IS_ENABLED]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCharacteristicGroupStoreTable(SchemaSetupInterface $setup)
    {
        $characteristicGroupStoreTable = $setup->getTable('engine_characteristic_group_store');
        $characteristicGroupTable = $setup->getTable('engine_characteristic_group');
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()->newTable(
            $characteristicGroupStoreTable
        )->addColumn(
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Characteristic Group Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            CharacteristicGroupInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Title'
        )->addColumn(
            CharacteristicGroupInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            1000,
            ['nullable' => true],
            'Description'
        )->addIndex(
            'idx_primary',
            ['store_id', CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID],
            ['type' => AdapterInterface::INDEX_TYPE_PRIMARY]
        )->addIndex(
            $setup->getIdxName(
                $characteristicGroupStoreTable,
                [CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID, 'store_id']
            ),
            [CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID, 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(
                $characteristicGroupStoreTable,
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
                $characteristicGroupTable,
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
            ),
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
            $characteristicGroupTable,
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $characteristicGroupStoreTable,
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