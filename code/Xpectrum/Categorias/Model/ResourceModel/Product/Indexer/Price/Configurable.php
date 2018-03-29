<?php
namespace Xpectrum\Categorias\Model\ResourceModel\Product\Indexer\Price;

class Configurable2 extends \Magento\ConfigurableProduct\Model\ResourceModel\Product\Indexer\Price\Configurable{

    protected function _applyConfigurableOption()
    {
        $metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
        $connection = $this->getConnection();
        $coaTable = $this->_getConfigurableOptionAggregateTable();
        $copTable = $this->_getConfigurableOptionPriceTable();
        $linkField = $metadata->getLinkField();

        $this->_prepareConfigurableOptionAggregateTable();
        $this->_prepareConfigurableOptionPriceTable();

        $subSelect = $this->getSelect();
        $subSelect->join(
            ['l' => $this->getTable('catalog_product_super_link')],
            'l.product_id = e.entity_id',
            []
        )->join(
            ['le' => $this->getTable('catalog_product_entity')],
            'le.' . $linkField . ' = l.parent_id',
            ['parent_id' => 'entity_id']
        );

        $select = $connection->select();
        $select->from(['sub' => new \Zend_Db_Expr('(' . (string)$subSelect . ')')], '')
            ->columns([
                'sub.parent_id',
                'sub.entity_id',
                'sub.customer_group_id',
                'sub.website_id',
                'sub.price',
                'sub.tier_price',
            ]);

        $query = $select->insertFromSelect($coaTable);
        $connection->query($query);

        $select = $connection->select()->from(
            [$coaTable],
            [
                'parent_id',
                'customer_group_id',
                'website_id',
                'MIN(price)',
                'MAX(price)',
                'MIN(tier_price)',
            ]
        )->group(
            ['parent_id', 'customer_group_id', 'website_id']
        );

        $query = $select->insertFromSelect($copTable);
        $connection->query($query);

        $table = ['i' => $this->_getDefaultFinalPriceTable()];
        $select = $connection->select()->join(
            ['io' => $copTable],
            'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id' .
            ' AND i.website_id = io.website_id',
            []
        );
        $select->columns(
            [
                'min_price' => 0,
                'max_price' => 1,
                'tier_price' => 'io.tier_price',
            ]
        );

        $query = $select->crossUpdateFromSelect($table);
        $connection->query($query);

        $connection->delete($coaTable);
        $connection->delete($copTable);

        return $this;
    }
}
