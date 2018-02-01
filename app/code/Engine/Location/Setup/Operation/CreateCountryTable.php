<?php
declare(strict_types=1);

namespace Engine\Location\Setup\Operation;

use Engine\Location\Api\Data\CountryInterface;
use Engine\Location\Model\Country\ResourceModel\Country as CountryResourceModel;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class CreateCountryTable
{
    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $countryTable = $this->createCountryTable($setup);

        $setup->getConnection()->createTable($countryTable);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return Table
     */
    private function createCountryTable(SchemaSetupInterface $setup): Table
    {
        $countryTable = $setup->getTable(CountryResourceModel::TABLE_NAME_COUNTRY);

        return $setup->getConnection()->newTable(
            $countryTable
        )->addColumn(
            CountryInterface::COUNTRY_ID,
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_IDENTITY => true,
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_PRIMARY => true,
            ],
            'Country Id'
        )->addColumn(
            CountryInterface::ENABLED,
            Table::TYPE_SMALLINT,
            1,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 1,
            ],
            'Is Enabled'
        )->addColumn(
            CountryInterface::POSITION,
            Table::TYPE_SMALLINT,
            3,
            [
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => 0,
            ],
            'Position'
        )->addColumn(
            CountryInterface::NAME,
            Table::TYPE_TEXT,
            255,
            [Table::OPTION_NULLABLE => false],
            'Name'
        )->addIndex(
            $setup->getIdxName($countryTable, [
                CountryInterface::ENABLED,
                CountryInterface::POSITION,
             ]),
            [CountryInterface::ENABLED, CountryInterface::POSITION]
        );
    }
}
