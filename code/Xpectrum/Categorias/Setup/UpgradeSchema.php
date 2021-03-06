<?php
/**
* Copyright © 2017 Xpectrum. All rights reserved.
* See COPYING.txt for license details.
*/
namespace Xpectrum\Categorias\Setup;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
    * {@inheritdoc}
    * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.5') < 0) {
            if (!$setup->tableExists('xpec_indexer_product')){
                $table = $setup->getConnection()
                    ->newTable('xpec_indexer_product')
                    ->addColumn(
                        'row_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'Row id'
                    )
                    ->addColumn(
                        'min_price',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        ['nullable' => false],
                        'Precio del Hijo'
                    )
                    ->addColumn(
                        'website_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'website_id'
                    )
                    ->addColumn(
                        'customer_group_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false],
                        'customer_group_id'
                    )
                    ->addIndex(
                        $setup->getIdxName(
                            'xpec_indexer_product',
                            ['row_id', 'website_id', 'customer_group_id'],
                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        ['row_id', 'website_id', 'customer_group_id'],
                        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                    )
                    ->setComment("Tabla indexer xpec precio hijo");
                $setup->getConnection()->createTable($table);
            }
        }
        $setup->endSetup();
    }
}