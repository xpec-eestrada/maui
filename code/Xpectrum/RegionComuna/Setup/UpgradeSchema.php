<?php

namespace Xpectrum\RegionComuna\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var string
     */
    private static $connectionName = 'xpectrum_shippingmethods';
    
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->createTableRegion($setup);
        }
        
        $setup->endSetup();
    }
    private function createTableRegion($setup){
        $table = $setup->getConnection()
            ->newTable($setup->getTable('xpec_regiones'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true,'nullable' => false,'primary' => true],
                'Id'
            )
            ->addColumn(
                'nombre',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                ['nullable' => false],
                'Nombre de la Región'
            )
            ->setComment("Xpec Regiones");
        $setup->getConnection()->createTable($table);
    }
}
