<?php

namespace Xpectrum\Categorias\Model;

class Marca{
    protected $_idtienda;

    public function __construct(
        $idtienda,
        array $data = []
    ){
        $this->_idtienda=$idtienda;
    }
    public function getValueAttributeMarca($id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('eav_attribute_option_value');
        $value='';
        if(isset($id) && is_numeric($id)){
            $sql='SELECT value FROM '.$tableName.' WHERE option_id='.$id;
            $result = $connection->fetchAll($sql);
            if(count($result)){
                foreach($result as $item){
                    $value=trim($item['value']);
                }
            }
        }
        
        $array=array();
        switch($value){
            case 'Nike':
                $array  = array('titulo'=>$value,'urllogo'=>'nike.png');
            break;
            case 'Quiksilver':
                $array  = array('titulo'=>$value,'urllogo'=>'quiksilver.png');
            break;
            case 'Kipling':
                $array  = array('titulo'=>$value,'urllogo'=>'kipling.png');
            break;
            case 'The North Face':
                $array  = array('titulo'=>$value,'urllogo'=>'the_north_face.png');
            break;
            case 'DC Shoes':
                $array  = array('titulo'=>$value,'urllogo'=>'dc_shoes.png');
            break;
            case 'Brooks Brothers':
                $array  = array('titulo'=>$value,'urllogo'=>'brooks_brothers.png');
            break;
            case 'Roxy':
                $array  = array('titulo'=>$value,'urllogo'=>'roxy.png');
            break;
            case 'GoPro':
                $array  = array('titulo'=>$value,'urllogo'=>'gopro.png');
            break;
            case 'UGG':
                $array  = array('titulo'=>$value,'urllogo'=>'ugg.png');
            break;
            case 'Timberland':
                $array  = array('titulo'=>$value,'urllogo'=>'timberland.png');
            break;
            case 'Kivul':
                $array  = array('titulo'=>$value,'urllogo'=>'kivul-ficha.png');
            break;
            default:
                $array  = array('titulo'=>'logo','urllogo'=>'');
            break;
        }
        return $array;
    }

}

