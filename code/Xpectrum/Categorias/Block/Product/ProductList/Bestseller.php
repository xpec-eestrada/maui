<?php

namespace Xpectrum\Categorias\Block\Product\ProductList;

use Magento\Catalog\Block\Product\Context;
use Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory;
use Magento\Catalog\Model\ProductRepository;
use Xpectrum\Categorias\Model\Marca;


class Bestseller extends \Magento\Framework\View\Element\Template{
    protected $_collectionFactory;
    protected $_productRepository;
    protected $_marca;
    protected $_baseUrl;
    protected $_imageHelper;

    public function __construct(
            Context $context,
            CollectionFactory $collectionFactory,
            ProductRepository $productRepository,
            array $data = []){
        $this->_collectionFactory = $collectionFactory;
        $this->_productRepository=$productRepository;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->_baseUrl=$storeManager->getStore()->getBaseUrl();
        $storeManagerInterface = $context->getStoreManager();
        $this->_marca=new Marca($storeManagerInterface->getStore()->getId());

        parent::__construct($context, $data);
    }
    public function getBestSellerData(){
        $collection = $this->_collectionFactory->create();
        $collection->setPeriod('month');
        $collection->getSelect()->limit(4);
        $data=array();
        foreach($collection as $item){
            try{
                $tmp=$this->_productRepository->getById($item->getProductId());
                if($tmp->getStatus()==1){
                    $data[]=$tmp;
                }
            }catch(\Magento\Framework\Exception\NoSuchEntityException $err){

            }
        }
        return $data;
    }
    public function getValueAttributeMarca($idmarca){
        return $this->_marca->getValueAttributeMarca($idmarca);
    }
    public function getImagenProducto($imagen){
        $url=$this->_baseUrl.'pub/media/catalog/product'.$imagen;
        return $url;
    }
    public function getUrlProductRelativ($idproduct){
        try{
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($idproduct);
            if(isset($product[0])){
                $producto = $this->_productRepository->getById($product[0]);
                return $producto->getUrlModel()->getUrl($producto);
            }else{
                $producto = $this->_productRepository->getById($idproduct);
                return $producto->getUrlModel()->getUrl($producto);
            }
        }catch(\Magento\Framework\Exception\NoSuchEntityException $err){

        }
    }
    public function getIdProductRelativ($idproduct){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($idproduct);
        if(isset($product[0])){
            $producto = $this->_productRepository->getById($product[0]);
            return $producto->getId();
        }else{
            return $idproduct;
        }        
    }
    public function getUrlBase(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl=$storeManager->getStore()->getBaseUrl();
        return $baseUrl;
    }
    public function logArray($array, $mode = 'a'){
        $pathFile = __DIR__ . 'log-' . date('Y-m-d') . '.txt';
        $handle = fopen($pathFile, $mode);
        fwrite($handle, print_r($array, true));
        fwrite($handle, "\n");
        fclose($handle);
    }

}