<?php
namespace Xpectrum\RegionComuna\Block\Address;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Magento\Customer\Block\Address\Edit{
    public function getComunaId(){
        $comuna_id = ($this->getAddress()->getRegionId()!==null)?$this->getAddress()->getRegionId():'';
        return $comuna_id;
    }
    public function getRegionId(){
        $region_id = ($this->getAddress()->getCustomAttribute('xpec_region')!==null)?$this->getAddress()->getCustomAttribute('xpec_region')->getValue():'';
        return $region_id;
    }
    public function getComunasByRegion(){
        $id_region = $this->getRegionId();
        $data      = array();
        if(isset($id_region) && $id_region!=null && is_numeric($id_region)){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
            $resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection    = $resource->getConnection();
            $tableName     = $resource->getTableName('xpec_comunas');
            $sql           ='SELECT id,nombre
                                FROM 
                                    '.$tableName.' 
                                WHERE 
                                    idregion='.$id_region.' 
                                ORDER BY
                                    nombre ASC';
            $result        = $connection->fetchAll($sql);
            foreach($result as $item){
                $data[]=array('label' => $item['nombre'],'value' => $item['id']);
            }
        }
        return $data;
    }
    public function getRegion(){
        $data      = array();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource      = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection    = $resource->getConnection();
        $tableName     = $resource->getTableName('xpec_regiones');
        $sql           ='SELECT id,nombre
                            FROM 
                                '.$tableName.' 
                            ORDER BY
                                nombre ASC';
        $result        = $connection->fetchAll($sql);
        foreach($result as $item){
            $data[]=array('label' => $item['nombre'],'value' => $item['id']);
        }
        return $data;
    }

}
