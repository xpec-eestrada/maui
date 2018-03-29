<?php

namespace Xpectrum\Categorias\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Xpectrum\Categorias\Model\Tecnologia;

class ModalProducto extends \Magento\Framework\App\Action\Action{

    protected $_resultPageFactory;
    protected $_baseUrl;
    protected $product;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory = $resultJsonFactory;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $this->_baseUrl=$storeManager->getStore()->getBaseUrl();
        parent::__construct($context);
    }
    /**
     * Index action
     *
     * @return $this
     */
    public function execute(){
        $post = $this->getRequest()->getPostValue();
        $idprodcuto=(isset($post) && isset($post['idproducto']) && is_numeric($post['idproducto']))?$post['idproducto']:0;
        $idtienda=(isset($post) && isset($post['idtienda']) && is_numeric($post['idtienda']))?$post['idtienda']:0;
        $body='';
        if($idprodcuto>0 && $idtienda>0){
            //$tecnologia=new Tecnologia($idtienda);
            //$tecnologia->getUrlImagenesTecnologia();

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->product = $objectManager->create('Magento\Catalog\Model\Product')->load($idprodcuto);
            //$product = $this->getProduct();
            $data    = $this->product->getData('tecnologia');
            $array   = explode(",",$data);
            $value   = array();
            foreach($array as $id){
                if(isset($id) && is_numeric($id)){
                    $sql='SELECT value FROM '.$tableName.' WHERE option_id='.$id.' AND store_id='.$currentStoreId;
                    $result = $connection->fetchAll($sql);
                    $url=$this->getUrlFab($result[0]['value']);
                    $value[]=array('id'=>$id,'url'=>$url,'name'=>$result[0]['value']);
                }
                
            }
            $body='<div class=\'container-image\'>
                    <img src=\''.$this->_baseUrl.'pub/media/catalog/product'.$this->product->getImage().'\'  />
            </div><div class=\'container-info\'></div>';
        }
        $result=$this->_resultPageFactory->create();
        $valor=array('body'=>$body);
        return $result->setData($valor);
    }
}