<?php

namespace Xpectrum\Categorias\Block\Product\ProductList;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Checkout\Model\ResourceModel\Cart;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Checkout\Model\Session;
use Magento\Framework\Module\Manager;
use Xpectrum\Categorias\Model\Marca;

class Upsell extends \Magento\Catalog\Block\Product\ProductList\Upsell {

    public function __construct(
        Context $context,
        Cart $checkoutCart,
        Visibility $catalogProductVisibility,
        Session $checkoutSession,
        Manager $moduleManager,
        // StoreManagerInterface $storeManagerInterface, 
        array $data = []
    ) {
        $this->_checkoutCart = $checkoutCart;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
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
        return '';
        //return $this->_marca->getValueAttributeMarca($idmarca);
    }

}