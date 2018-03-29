<?php
namespace Xpectrum\Categorias\Block\Product\View;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;

class Marca extends \Magento\Framework\View\Element\Template{
    protected $registry;
    protected $_storeManagerInterface;
    protected $_product;
    public function __construct(
            Context $context,
            Registry $registry,
            array $data = []){
        $this->registry=$registry;
        $storeManagerInterface = $context->getStoreManager();
        $this->_storeManagerInterface=$storeManagerInterface;
        $_product = null;
        $this->_product = $this->registry->registry('product');
        parent::__construct($context, $data);
    }
    public function getImageMarca(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute_option_value');

        $currentStore = $this->_storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();

        $id=(isset($this->_product)) ? $this->_product->getData('marca') : 0;
        $name='';
        if(isset($id) && is_numeric($id) ){
            $sql='SELECT value FROM '.$tableName.' WHERE option_id='.$id;
            $result = $connection->fetchAll($sql);
            $name = count($result) ? $result[0]['value'] : '';
        }
        return $this->getUrlMarcaFicha($name);
    }
    protected function getUrlMarcaFicha($name){
        $img='';
        switch($name){
            case 'Nike':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/nike.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Quiksilver':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/quiksilver.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Kipling':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/kipling.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'The North Face':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/the_north_face.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'DC Shoes':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/dc_shoes.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Brooks Brothers':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/brooks_brothers.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Roxy':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/roxy.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'GoPro':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/gopro.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'UGG':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/ugg.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Timberland':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/timberland.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            case 'Kivul':
                $img='<div class="img-marca-ficha">
                    <img src="pub/static/frontend/Xpectrum/kivul/es_CL/Xpectrum_Categorias/images/kivul-ficha.png" alt="'.$name.'" />
                </div>';
                return $img;
            break;
            default:
                return '';
            break;
        }
    }
    public function getNombreProducto(){
        $data='';
        if(isset($this->_product)){
            $data = '<div class="lab-nombre"><span>'.$this->_product->getData('name').'</span></div>';
        }
        return $data;
    }
    public function getSkuProducto(){
        $data='';
        if(isset($this->_product)){
            $data = '<div class="lab-sku">(C&oacute;digo:<span> '.$this->_product->getData('sku').'</span>)</div>';
        }
        return $data;
    }
    public function getDescripcionCorta(){
        $data='desc';
        if(isset($this->_product)){
            $desc=str_replace('-/-','</br>',$this->_product->getData('short_description'));
            $data = '<div class="lab-desc-cor"><span> '.$desc.'</span></div>';
        }
        return $data;
    }
    public function getMegustaFB(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $url=$storeManager->getStore()->getBaseUrl();
        return '
        <div id="fb-root"></div>
        <div class="fb-like" data-href="'.$this->_product->getProductUrl().'" data-layout="button_count" data-action="like" data-size="small" data-show-faces="true" data-share="true"></div>';
    }
}