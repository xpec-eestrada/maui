<?php
namespace Xpectrum\Categorias\Block\Product\View;

use Magento\Catalog\Model\Product;

class Caracteristicas extends \Magento\Framework\View\Element\Template{
    protected $_product = null;
    protected $_coreRegistry = null;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    public function getProduct(){
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }
    public function getDescripcion(){
        $product = $this->getProduct();
        $text    = str_replace('-/-','</br>',$product->getShortDescription());
        return $text;
    }
    public function getDescripcionLarga(){
        $product = $this->getProduct();
        $text    = str_replace('-/-','</br>',$product->getDescription());
        return $text;
    }
    public function getAdditionalData(array $excludeAttr = []){
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ((string)$value == '') {
                    $value = __('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                if (($value instanceof Phrase || is_string($value)) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }
        return $data;
    }
    public function getEspicificaciones(){
        $product    = $this->getProduct();
        $var        = str_replace('-/-','</br>',$product->getData('especificacion'));
        return $var;
    }

}
