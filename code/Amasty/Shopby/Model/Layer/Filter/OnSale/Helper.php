<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Layer\Filter\OnSale;

use Magento\CatalogRule\Pricing\Price\CatalogRulePrice;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product as ConfigurableProduct;

class Helper
{
    /** @var ConfigurableProduct\CollectionFactory  */
    private $configurableCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;

    public function __construct(
        ConfigurableProduct\CollectionFactory $configurableCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
    ) {
        $this->configurableCollectionFactory = $configurableCollectionFactory;
        $this->storeManager = $storeManager;
        $this->resource = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->dateTime = $dateTime;
        $this->localeDate = $localeDate;
    }

    public function addOnSaleFilter(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        $configurableCollection = $this->configurableCollectionFactory->create();
        $configurableCollection->getSelect()->group('e.entity_id');
        $configurableCollection->load();

        if (!$configurableCollection->hasFlag('catalog_rule_loaded')) {
            $this->loadCatalogRule($configurableCollection);
        }

        $collection->addPriceData();

        $select = $collection->getSelect();

        $select->joinLeft(
            ['catalog_rule' => $collection->getTable('catalogrule_product_price')],
            'catalog_rule.product_id = e.entity_id  
            AND catalog_rule.website_id = 1
            AND catalog_rule.customer_group_id = 0
            AND (catalog_rule.latest_start_date < NOW()
            OR catalog_rule.latest_start_date IS NULL)
            AND (catalog_rule.earliest_end_date > NOW()
            OR catalog_rule.earliest_end_date IS NULL)',
            null
        );

        $select->joinLeft(
            ['relation' => $collection->getTable('catalog_product_relation')],
            'relation.child_id = e.entity_id',
            ['parent_id' => 'relation.parent_id']
        );


        $select->where('ifnull(catalog_rule.rule_price, price_index.final_price) < price_index.price');
        $select->group('e.entity_id');
    }

    protected function loadCatalogRule(ConfigurableProduct\Collection $productCollection)
    {
        if (!$productCollection->hasFlag('catalog_rule_loaded')) {
            $connection = $this->resource->getConnection();
            $store = $this->storeManager->getStore();
            $productCollection->getSelect()
                ->joinLeft(
                    ['catalog_rule' => $this->resource->getTableName('catalogrule_product_price')],
                    implode(' AND ', [
                        'catalog_rule.product_id = e.entity_id',
                        $connection->quoteInto('catalog_rule.website_id = ?', $store->getWebsiteId()),
                        $connection->quoteInto(
                            'catalog_rule.customer_group_id = ?',
                            $this->customerSession->getCustomerGroupId()
                        ),
                        $connection->quoteInto(
                            'catalog_rule.rule_date = ?',
                            $this->dateTime->formatDate($this->localeDate->scopeDate($store->getId()), false)
                        ),
                    ]),
                    [CatalogRulePrice::PRICE_CODE => 'rule_price']
                );
            $productCollection->setFlag('catalog_rule_loaded', true);
        }
    }
}
