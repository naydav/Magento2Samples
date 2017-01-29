<?php
namespace Engine\CategoryCharacteristicGroup\Setup;

use Engine\Category\Api\Data\CategoryInterface;
use Engine\CharacteristicGroup\Api\Data\CharacteristicGroupInterface;
use Engine\CategoryCharacteristicGroup\Api\Data\RelationInterface;
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
        $setup->getConnection()->createTable($this->createCategoryCharacteristicRelationTable($setup));
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCategoryCharacteristicRelationTable(SchemaSetupInterface $setup)
    {
        $relationTable = $setup->getTable('engine_category_characteristic_group_relation');
        $categoryTable = $setup->getTable('engine_category');
        $characteristicGroupTable = $setup->getTable('engine_characteristic_group');

        return $setup->getConnection()->newTable(
            $relationTable
        )->addColumn(
            RelationInterface::RELATION_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Relation id'
        )->addColumn(
            RelationInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Category id'
        )->addColumn(
            RelationInterface::CHARACTERISTIC_GROUP_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Characteristic Group id'
        )->addColumn(
            RelationInterface::CHARACTERISTIC_GROUP_POSITION,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Characteristic Group Position'
        )->addIndex(
            $setup->getIdxName(
                $categoryTable,
                [
                    RelationInterface::CHARACTERISTIC_GROUP_ID,
                    RelationInterface::CATEGORY_ID,
                ],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                RelationInterface::CATEGORY_ID,
            ],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName(
                $relationTable,
                [
                    RelationInterface::CATEGORY_ID,
                    RelationInterface::CHARACTERISTIC_GROUP_POSITION,
                ]
            ),
            [
                RelationInterface::CATEGORY_ID,
                RelationInterface::CHARACTERISTIC_GROUP_POSITION,
            ]
        )->addForeignKey(
            $setup->getFkName(
                $relationTable,
                RelationInterface::CATEGORY_ID,
                $categoryTable,
                CategoryInterface::CATEGORY_ID
            ),
            RelationInterface::CATEGORY_ID,
            $categoryTable,
            CategoryInterface::CATEGORY_ID,
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $relationTable,
                RelationInterface::CHARACTERISTIC_GROUP_ID,
                $characteristicGroupTable,
                CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID
            ),
            RelationInterface::CHARACTERISTIC_GROUP_ID,
            $characteristicGroupTable,
            CharacteristicGroupInterface::CHARACTERISTIC_GROUP_ID,
            AdapterInterface::FK_ACTION_CASCADE
        );
    }
}
