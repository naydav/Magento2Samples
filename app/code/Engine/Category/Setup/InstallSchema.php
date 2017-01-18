<?php
namespace Engine\Category\Setup;

use Engine\Category\Api\Data\CategoryInterface;
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
        $setup->getConnection()->createTable($this->createCategoryTable($setup));
        $setup->getConnection()->createTable($this->createCategoryStoreTable($setup));
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCategoryTable(SchemaSetupInterface $setup)
    {
        $categoryTable = $setup->getTable('engine_category');

        return $setup->getConnection()->newTable(
            $categoryTable
        )->addColumn(
            CategoryInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Category Id'
        )->addColumn(
            CategoryInterface::PARENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Parent id'
        )->addColumn(
            'position',
            Table::TYPE_SMALLINT,
            4,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Position'
        )->addColumn(
            CategoryInterface::URL_KEY,
            Table::TYPE_TEXT,
            100,
            [],
            'Url key'
        )->addColumn(
            CategoryInterface::IS_ANCHOR,
            Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => 0],
            'Is Anchor'
        )->addColumn(
            CategoryInterface::IS_ENABLED,
            Table::TYPE_SMALLINT,
            1,
            ['unsigned' => true, 'nullable' => false, 'default' => 1],
            'Is Enabled'
        )->addIndex(
            $setup->getIdxName(
                $categoryTable,
                [
                    CategoryInterface::PARENT_ID,
                    CategoryInterface::URL_KEY,
                ],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [CategoryInterface::PARENT_ID, CategoryInterface::URL_KEY],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName($categoryTable, [
                CategoryInterface::PARENT_ID,
                CategoryInterface::POSITION,
            ]),
            [
                CategoryInterface::PARENT_ID,
                CategoryInterface::POSITION,
            ]
        )->addForeignKey(
            $setup->getFkName(
                $categoryTable,
                CategoryInterface::PARENT_ID,
                $categoryTable,
                CategoryInterface::CATEGORY_ID
            ),
            CategoryInterface::PARENT_ID,
            $categoryTable,
            CategoryInterface::CATEGORY_ID,
            AdapterInterface::FK_ACTION_CASCADE
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCategoryStoreTable(SchemaSetupInterface $setup)
    {
        $categoryStoreTable = $setup->getTable('engine_category_store');
        $categoryTable = $setup->getTable('engine_category');
        $storeTable = $setup->getTable('store');

        return $setup->getConnection()->newTable(
            $categoryStoreTable
        )->addColumn(
            CategoryInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Category Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            CategoryInterface::TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Title'
        )->addColumn(
            CategoryInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            1000,
            ['nullable' => true],
            'Description'
        )->addIndex(
            'idx_primary',
            ['store_id', CategoryInterface::CATEGORY_ID],
            ['type' => AdapterInterface::INDEX_TYPE_PRIMARY]
        )->addIndex(
            $setup->getIdxName(
                $categoryStoreTable,
                [CategoryInterface::CATEGORY_ID, 'store_id']
            ),
            [CategoryInterface::CATEGORY_ID, 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(
                $categoryStoreTable,
                CategoryInterface::CATEGORY_ID,
                $categoryTable,
                CategoryInterface::CATEGORY_ID
            ),
            CategoryInterface::CATEGORY_ID,
            $categoryTable,
            CategoryInterface::CATEGORY_ID,
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $categoryStoreTable,
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
