<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Layer\Filter;

use Amasty\Shopby\Model\Layer\Filter\OnSale\Helper;

class OnSale extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    use \Amasty\Shopby\Model\Layer\Filter\Traits\FilterTrait;

    const FILTER_ON_SALE = 1;

    protected $attributeCode = 'on_sale';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $attributeValue;

    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    /** @var \Amasty\Shopby\Model\Request  */
    protected $shopbyRequest;

    /** @var Helper  */
    private $helper;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar = 'on_sale';
        $this->scopeConfig = $scopeConfig;
        $this->aggregationAdapter = $aggregationAdapter;
        $this->shopbyRequest = $shopbyRequest;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }
        $filter = $this->shopbyRequest->getFilterParam($this);

        if (!in_array($filter, [self::FILTER_ON_SALE])) {
            return $this;
        }

        $this->setCurrentValue($filter);

        if ($filter == self::FILTER_ON_SALE) {
            $this->helper->addOnSaleFilter($this->getLayer()->getProductCollection());

            $name = __('Yes');
            $this->getLayer()->getState()->addFilter($this->_createItem($name, $filter));
        }

        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        $label = $this->scopeConfig->getValue('amshopby/on_sale_filter/label', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $label;
    }

    public function getPosition()
    {
        $position = (int) $this->scopeConfig->getValue('amshopby/on_sale_filter/position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $position;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $productCollectionOrigin = $this->getLayer()
            ->getProductCollection();

        if ($this->hasCurrentValue()) {
            $requestBuilder = clone $productCollectionOrigin->getMemRequestBuilder();
            $requestBuilder->removePlaceholder($this->attributeCode);
            $queryRequest = $requestBuilder->create();
            $optionsFacetedData = $this->aggregationAdapter->getBucketByRequest($queryRequest, $this->attributeCode);
        } else {
            $collection = $productCollectionOrigin;
            $optionsFacetedData = $collection->getFacetedData($this->attributeCode);
        }

        $isOnSale = isset($optionsFacetedData[1]) ? $optionsFacetedData[1]['count'] : 0;

        $listData = [
            [
                'label' => __('On Sale'),
                'value' => self::FILTER_ON_SALE,
                'count' => $isOnSale,
            ]
        ];

        foreach ($listData as $data) {
            if($data['count'] < 1) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
    }
}
