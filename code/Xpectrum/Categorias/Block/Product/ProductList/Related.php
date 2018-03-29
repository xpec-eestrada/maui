<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Xpectrum\Categorias\Block\Product\ProductList;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Catalog\Block\Product\Context;
use Magento\Checkout\Model\ResourceModel\Cart;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Model\Session;
use Magento\Framework\Module\Manager;
use Xpectrum\Categorias\Model\Marca;
use Magento\Store\Model\StoreManagerInterface;

class Related extends \Magento\Catalog\Block\Product\ProductList\Related{
    protected $_ListProduct;
    protected $_marca;

    public function __construct(
        Context $context,
        Cart $checkoutCart,
        Visibility $catalogProductVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        // StoreManagerInterface $storeManagerInterface,
        array $data = []
    ){
        $storeManagerInterface = $context->getStoreManager();

        $this->_marca=new Marca($storeManagerInterface->getStore()->getId());
        parent::__construct(
            $context,
            $checkoutCart,
            $catalogProductVisibility,
            $checkoutSession,
            $moduleManager,
            $data
        );
    }
    public function _toHtml(){
        return parent::_toHtml();
    }
    public function getValueAttributeMarca($idmarca){
        return $idmarca.'hols';
        //return $this->_marca->getValueAttributeMarca($idmarca);
    }
}
