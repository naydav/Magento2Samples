<?php
declare(strict_types=1);

namespace Engine\Location\Setup;

use Engine\Location\Setup\Operation\CreateCountryTable;
use Engine\Location\Setup\Operation\CreateRegionTable;
use Engine\Location\Setup\Operation\CreateCityTable;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author naydav <valeriy.nayda@gmail.com>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var CreateCountryTable
     */
    private $createCountryTable;

    /**
     * @var CreateRegionTable
     */
    private $createRegionTable;

    /**
     * @var CreateCityTable
     */
    private $createCityTable;

    /**
     * @param CreateCountryTable $createCountryTable
     * @param CreateRegionTable $createRegionTable
     * @param CreateCityTable $createCityTable
     */
    public function __construct(
        CreateCountryTable $createCountryTable,
        CreateRegionTable $createRegionTable,
        CreateCityTable $createCityTable
    ) {
        $this->createCountryTable = $createCountryTable;
        $this->createRegionTable = $createRegionTable;
        $this->createCityTable = $createCityTable;
    }

    /**
     * @inheritdoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createCountryTable->execute($setup);
        $this->createRegionTable->execute($setup);
        $this->createCityTable->execute($setup);

        $setup->endSetup();
    }
}