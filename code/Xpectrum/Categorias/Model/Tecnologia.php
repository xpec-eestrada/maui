<?php

namespace Xpectrum\Categorias\Model;

class Tecnologia{
    protected $_idtienda;

    public function __construct(
        $idtienda,
        array $data = []
    ){
        $this->_idtienda=$idtienda;
    }
    public function getUrlImagenesTecnologia($id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute_option_value');
        

        $product = $this->getProduct();
        $data=$product->getData('tecnologia');
        $array=explode(",",$data);

        $value=array();
        foreach($array as $id){
            if(isset($id) && is_numeric($id)){
                $sql='SELECT value FROM '.$tableName.' WHERE option_id='.$id.' AND store_id='.$_idtienda;
                $result = $connection->fetchAll($sql);
                $url=$this->getUrlTec($result[0]['value']);
                $value[]=array('id'=>$id,'url'=>$url,'name'=>$result[0]['value']);
            }
            
        }
        return $value;
    }
    public function getUrlTec($name){
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

}

