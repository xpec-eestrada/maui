<?php
namespace Xpectrum\Categorias\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Store\Model\StoreManagerInterface;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery {

    private $_storeManagerInterface;
    public $_productRepository;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->_productRepository=$productRepository;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->_storeManagerInterface=$storeManager;
        parent::__construct($context, $arrayUtils,$jsonEncoder,$data);
    }
    public function _toHtml(){
        return parent::_toHtml();
    }
    
    public function getUrlImagenesTecnologia(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute_option_value');
        
        $currentStore = $this->_storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();

        $product = $this->getProduct();
        $data=$product->getData('tecnologia');
        $array=explode(",",$data);

        $value=array();
        foreach($array as $id){
            if(isset($id) && is_numeric($id)){
                $sql='SELECT value FROM '.$tableName.' WHERE option_id='.$id.' AND store_id='.$currentStoreId;
                $result = $connection->fetchAll($sql);
                $url=$this->getUrlFab($result[0]['value']);
                $value[]=array('id'=>$id,'url'=>$url,'name'=>$result[0]['value']);
            }
            
        }
        return $value;
    }
    public function getUrlFab($name){
        switch($name){
            case 'Warm Comfort':
                return 'fab-01.png';
            break;
            case 'Hidro Seam':
                return 'fab-02.png';
            break;
            default:
                return '';
            break;
        }
    }
    public function getUrlGuiaTalla(){
        $product = $this->getProduct();
        return trim($product->getData('guia_de_talla'));
    }
    public function tienePrecioEspecial(){
        $_product = $this->getProduct();
        $sw=false;
        $label='';
        
        try{
            $skuchild=$_product->getData('child_color_sku');
            if( !empty($skuchild) ){
                $child=$this->_productRepository->get($skuchild);
                $_finalPrice    = $child->getFinalPrice();
                $_price         = $child->getPrice();
                if($_finalPrice < $_price  ){
                    $sw=true;
                    $label=$child->getData('discount_percent');
                }
            }else{
                $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); 
                $StockState     = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
                $enable         = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                $_children=array();
                if(method_exists($_product->getTypeInstance(),"getUsedProducts")){
                    $_children = $_product->getTypeInstance()->getUsedProducts($_product);
                }   
                if(isset($_children) && is_array($_children) && (count($_children)>0 ) ){
                    foreach( $_children as $key => $_child ){
                        if($_product->getData('color')==$_child->getData('color')){
                            $_finalPrice    = $_child->getFinalPrice();
                            $_price         = $_child->getPrice();
                            $stock          = $StockState->getStockQty($_child->getId(), $_child->getStore()->getWebsiteId());
                            if( ($_finalPrice < $_price) && $_child->getStatus()==$enable && $stock>0  ){
                                $sw=true;
                                $label=$_child->getData('discount_percent');
                                break;
                            }
                        }
                    }             
                }else{
                    $_finalPrice=$_product->getFinalPrice();
                    $_price=$_product->getPrice();
                    if($_finalPrice < $_price){
                        $sw=true;
                        $label=$_product->getData('discount_percent');
                    }
                }
            }

            
        }catch(Exception $err){
            
        }
        return array('precioEspecial'=>$sw,'labelDescuento'=>$label);
    }
    
}