<?php
namespace Xpectrum\Categorias\Model\Indexer;

use Magento\Framework\Indexer\CacheContext;
class XpecProductPrice implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    private $cacheContext;
    public $logger;
    public function __construct(
        \Xpectrum\Categorias\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }
    public function execute($ids)
    {
    }
    public function executeFull(){
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $i=0;
        try {
            $this->logger->info(date("Y-m-d H:i:s")."  Start Transaction...");
            $connection->beginTransaction();
            
            $tableskuchild  = $resource->getTableName('catalog_product_entity_varchar');
            $tabledesc      = $resource->getTableName('catalog_product_entity_decimal');
            $tabledescdate  = $resource->getTableName('catalog_product_entity_datetime');
            $tableprice     = $tabledesc;
            $tableprod      = $resource->getTableName('catalog_product_entity');
            $tableindexer   = $resource->getTableName('xpec_indexer_product');

            $idattribsku    = $this->idAttribute($resource,$connection,'child_color_sku');
            $idspecialprice = $this->idAttribute($resource,$connection,'special_price');
            $idfromspecial  = $this->idAttribute($resource,$connection,'special_from_date');
            $idtopecial     = $this->idAttribute($resource,$connection,'special_to_date');
            $idprice        = $this->idAttribute($resource,$connection,'price');

            $stores         = $this->getStores($resource,$connection);
            $idsgroup       = $this->getCustomerGroups($resource,$connection);
            
            $sql            = 'SELECT entity_id,type_id,sku FROM '.$tableprod;
            $result         = $connection->fetchAll($sql);

            $sql="DELETE FROM ".$tableindexer;
            $connection->query($sql);

            if( count($result) ){
                foreach($result as $product){
                    $i++;
                    $entity_id = $product['entity_id'];
                    $sku    = $product['sku'];
                    switch($product['type_id']){
                        case 'configurable':
                            foreach($stores as $store){
                                $sql        = 'SELECT value FROM '.$tableskuchild.' WHERE entity_id='.$entity_id.' AND attribute_id='.$idattribsku.' AND store_id='.$store;
                                $skuchil    = $connection->fetchOne($sql);
                                if( !empty($skuchil) && $sku!=$skuchil ){//valida que exista un skuchild
                                    $array = array();
                                    $array['idspecialprice'] = $idspecialprice;
                                    $array['idfromspecial']  = $idfromspecial;
                                    $array['tabledescdate']  = $tabledescdate;
                                    $array['idtopecial']     = $idtopecial;
                                    $array['tableprod']      = $tableprod;
                                    $array['tabledesc']      = $tabledesc;
                                    $array['store']          = $store;
                                    $array['sku']            = $skuchil;
                                    $rschild = $this->getProductInfo($connection,$array);
                                    if(count($rschild)){
                                        foreach($rschild as $itemchild){//obtiene data del skuchild
                                            $special    = $itemchild['special'];
                                            $from       = $itemchild['special_from'];
                                            $to         = $itemchild['special_to'];
                                            if(isset($special) && $special!=null && !empty($special) ){// valida si el sku tiene special price.
                                                $array=array();
                                                $array['tableindexer'] = $tableindexer;
                                                $array['idsgroup']     = $idsgroup;
                                                $array['special']      = $special;
                                                $array['entity_id']       = $entity_id;
                                                $array['store']        = $store;
                                                $array['from']         = $from;
                                                $array['to']           = $to;
    
                                                $this->setPriceSpecial($connection,$array);
                                            }else{//si no tiene special price se obtienen los valores base.
                                                $array = array();
                                                $array['tableindexer'] = $tableindexer;
                                                $array['tableprice']   = $tableprice;
                                                $array['idprice']      = $idprice;
                                                $array['parent']       = $entity_id;
                                                $array['entity_id']       = $itemchild['entity_id'];
                                                $array['store']        = $store;
                                                $this->setPriceNormalParent($connection,$idsgroup,$array);
                                            }
                                        }
                                    }else{
                                        $array = array();
                                        $array['current_price_id'] = $idprice;
                                        $array['special_price_id'] = $idspecialprice;
                                        $array['from_price_id']    = $idfromspecial;
                                        $array['tableindexer']     = $tableindexer;
                                        $array['to_price_id']      = $idtopecial;
                                        $array['parent_id']        = $entity_id;
                                        $array['store_id']         = $store;
                                        $array['grupo']            = $idsgroup;
                                        $this->setPriceParentChild($connection,$array);
                                    }
                                }else{
                                    $array = array();
                                    $array['current_price_id'] = $idprice;
                                    $array['special_price_id'] = $idspecialprice;
                                    $array['from_price_id']    = $idfromspecial;
                                    $array['tableindexer']     = $tableindexer;
                                    $array['to_price_id']      = $idtopecial;
                                    $array['parent_id']        = $entity_id;
                                    $array['store_id']         = $store;
                                    $array['grupo']            = $idsgroup;
                                    $this->setPriceParentChild($connection,$array);
                                }
                            }
                        break;
                        case 'simple':
                            foreach($stores as $store){
                                $array = array();
                                $array['idspecialprice'] = $idspecialprice;
                                $array['idfromspecial']  = $idfromspecial;
                                $array['tabledescdate']  = $tabledescdate;
                                $array['idtopecial']     = $idtopecial;
                                $array['tableprod']      = $tableprod;
                                $array['tabledesc']      = $tabledesc;
                                $array['store']          = $store;
                                $array['sku']            = $sku;
                                $rschild = $this->getProductInfo($connection,$array);
                                if(count($rschild)){
                                    foreach($rschild as $itemchild){//obtiene data del sku
                                        $special    = $itemchild['special'];
                                        $from       = $itemchild['special_from'];
                                        $to         = $itemchild['special_to'];
                                        if(isset($special) && $special!=null && !empty($special) ){// valida si el sku tiene special price.
                                            $array=array();
                                            $array['tableindexer'] = $tableindexer;
                                            $array['idsgroup']     = $idsgroup;
                                            $array['special']      = $special;
                                            $array['entity_id']       = $entity_id;
                                            $array['store']        = $store;
                                            $array['from']         = $from;
                                            $array['to']           = $to;
    
                                            $this->setPriceSpecial($connection,$array);
                                        }else{//si no tiene special price se obtienen los valores base.
                                            $array = array();
                                            $array['tableindexer'] = $tableindexer;
                                            $array['tableprice']   = $tableprice;
                                            $array['idprice']      = $idprice;
                                            $array['entity_id']       = $entity_id;
                                            $array['store']        = $store;
                                            $this->setPriceNormal($connection,$idsgroup,$array);
                                        }
                                    }
                                }else{
                                    $array = array();
                                    $array['tableindexer'] = $tableindexer;
                                    $array['tableprice']   = $tableprice;
                                    $array['idprice']      = $idprice;
                                    $array['entity_id']       = $entity_id;
                                    $array['store']        = $store;
                                    $this->setPriceNormal($connection,$idsgroup,$array);
                                }
                            }
                        break;
                    }
                }
            }
            $connection->commit();
            $this->logger->info(date("Y-m-d H:i:s")."  End Transaction...");
            $this->logger->info("Registros leidos:  ".$i);
        } catch (\Exception $e) {
            $connection->rollBack();
            $this->logger->info(date("Y-m-d H:i:s")." - Error : ".$e->getMessage());
        }
    }
    public function idAttribute($resource,$connection,$attribute){
        try{
            $tableName  = $resource->getTableName('eav_attribute');
            $sql        = 'SELECT attribute_id FROM '.$tableName.' WHERE attribute_code=\''.$attribute.'\'';
            $data       = $connection->fetchOne($sql);
            return $data;
        }catch(\Exception $e){
            $this->logger->info("Function idAttribute Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function getStores($resource,$connection){
        try{
            $array=array();
            $tableName  = $resource->getTableName('store');
            $sql        = 'SELECT store_id FROM '.$tableName.' WHERE is_active=1';
            $data       = $connection->fetchAll($sql);
            foreach($data as $item){
                $array[]=$item['store_id'];
            }
            return $array;
        }catch(\Exception $e){
            $this->logger->info("Function getStores Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function getCustomerGroups($resource,$connection){
        try{
            $array=array();
            $tableName  = $resource->getTableName('customer_group');
            $sql        = 'SELECT customer_group_id FROM '.$tableName;
            $data       = $connection->fetchAll($sql);
            foreach($data as $item){
                $array[]=$item['customer_group_id'];
            }
            return $array;
        }catch(\Exception $e){
            $this->logger->info("Function getCustomerGroups Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function setPriceNormal($connection,$idsgroup,$array){
        $tableindexer   = $array['tableindexer'];
        $tableprice     = $array['tableprice'];
        $idprice        = $array['idprice'];
        $entity_id         = $array['entity_id'];
        $store          = $array['store'];
        try{
            $sql    = 'SELECT tprice.value FROM '.$tableprice.' tprice
            WHERE entity_id='.$entity_id.' AND attribute_id='.$idprice.' AND (store_id='.$store.' OR store_id=0)';
            $price  = $connection->fetchOne($sql);
            if(isset($price) && is_numeric($price)){
                $count  = 0;
                $values = '';
                foreach($idsgroup as $group){
                    if($count>0){
                        $values=$values.',('.$entity_id.','.$price.','.$store.','.$group.')';
                    }else{
                        $values='('.$entity_id.','.$price.','.$store.','.$group.')';
                    }
                    $count++;
                }
                $sql="INSERT INTO ".$tableindexer.'(row_id,min_price,website_id,customer_group_id) 
                        VALUES '.$values;
                $connection->query($sql);
            }
        }catch(\Exception $e){
            $this->logger->info("Function setPriceNormal Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function setPriceNormalParent($connection,$idsgroup,$array){
        $tableindexer   = $array['tableindexer'];
        $tableprice     = $array['tableprice'];
        $idprice        = $array['idprice'];
        $entity_id         = $array['entity_id'];
        $parent         = $array['parent'];
        $store          = $array['store'];
        try{
            $sql    = 'SELECT tprice.value FROM '.$tableprice.' tprice
            WHERE entity_id='.$entity_id.' AND attribute_id='.$idprice.' AND (store_id='.$store.' OR store_id=0)';
            $price  = $connection->fetchOne($sql);
            if(isset($price) && is_numeric($price)){
                $count  = 0;
                $values = '';
                foreach($idsgroup as $group){
                    if($count>0){
                        $values=$values.',('.$parent.','.$price.','.$store.','.$group.')';
                    }else{
                        $values='('.$parent.','.$price.','.$store.','.$group.')';
                    }
                    $count++;
                }
                $sql="INSERT INTO ".$tableindexer.'(row_id,min_price,website_id,customer_group_id) 
                        VALUES '.$values;
                $connection->query($sql);
            }
        }catch(\Exception $e){
            $this->logger->info("Function setPriceNormal Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function getProductInfo($connection,$array){
        $idspecialprice = $array['idspecialprice'];
        $idfromspecial  = $array['idfromspecial'];
        $tabledescdate  = $array['tabledescdate'];
        $idtopecial     = $array['idtopecial'];
        $tableprod      = $array['tableprod'];
        $tabledesc      = $array['tabledesc'];
        $store          = $array['store'];
        $sku            = $array['sku'];

        try{
            $sql    = 'SELECT 
                        e.entity_id,e.entity_id,e.sku,e.type_id,fr.value as special_from,tto.value as special_to,spe.value as special
                    FROM '.$tableprod.' e 
                    LEFT JOIN '.$tabledesc.' spe ON(spe.entity_id=e.entity_id AND spe.attribute_id='.$idspecialprice.' AND spe.store_id='.$store.')
                    LEFT JOIN '.$tabledescdate.' fr ON(fr.entity_id=e.entity_id AND fr.attribute_id='.$idfromspecial.' AND fr.store_id='.$store.')
                    LEFT JOIN '.$tabledescdate.' tto ON(tto.entity_id=e.entity_id AND tto.attribute_id='.$idtopecial.' AND tto.store_id='.$store.')
                    WHERE 
                        e.sku=\''.$sku.'\'';
            return $result    = $connection->fetchAll($sql);
        }catch(\Exception $e){
            $this->logger->info("Function getProductInfo Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function setPriceSpecial($connection,$array){
        $tableindexer = $array['tableindexer'];
        $idsgroup     = $array['idsgroup'];
        $special      = $array['special'];
        $entity_id       = $array['entity_id'];
        $store        = $array['store'];
        $from         = $array['from'];
        $to           = $array['to'];
        try{
            if(isset($from) && $from!=null && !empty($from) && isset($to) && $to!=null && !empty($to) ){//valida si estan establecida las fecha desde y hasta del precio especial
                $from   = strtotime($from);
                $to     = strtotime($to);
                $fecha  = strtotime(date("Y-m-d H:i:s"));
                if($fecha>=$from && $fecha<$to){//valida que fecha actual este dentro del rango de fecha del precio especial.
                    $count=0;
                    $values='';
                    foreach($idsgroup as $group){
                        if($count>0){
                            $values=$values.',('.$entity_id.','.$special.','.$store.','.$group.')';
                        }else{
                            $values='('.$entity_id.','.$special.','.$store.','.$group.')';
                        }
                        $count++;
                    }
                    $sql="INSERT INTO ".$tableindexer.'(row_id,min_price,website_id,customer_group_id) 
                            VALUES '.$values;
                    $connection->query($sql);
                }
            }else{
                if(isset($from) && $from!=null && !empty($from)){//valida que solo este la fecha de inicio
                    $from   = strtotime($from);
                    $fecha  = strtotime(date("Y-m-d H:i:s"));
                    if($fecha>=$from){//valida que fecha actual sea mayor a fecha de inicio
                        $count=0;
                        $values='';
                        foreach($idsgroup as $group){//Se recorren todos los grupos existentes.
                            if($count>0){
                                $values=$values.',('.$entity_id.','.$special.','.$store.','.$group.')';
                            }else{
                                $values='('.$entity_id.','.$special.','.$store.','.$group.')';
                            }
                            $count++;
                        }
                        $sql="INSERT INTO ".$tableindexer.'(row_id,min_price,website_id,customer_group_id) 
                                VALUES '.$values;
                        $connection->query($sql);
                    }
                }
            }
        }catch(\Exception $e){
            $this->logger->info("Function setPriceSpecial Error: ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
        
        
    }
    public function setPriceParentChild($connection,$array){
        $current_price_id = $array['current_price_id'];
        $special_price_id = $array['special_price_id'];
        $from_price_id    = $array['from_price_id'];
        $tableindexer     = $array['tableindexer'];
        $to_price_id      = $array['to_price_id'];
        $parent_id        = $array['parent_id'];
        $min_value        = 0;
        $store_id         = $array['store_id'];
        $grupos           = $array['grupo'];

        try{
            $sql='SELECT tprecios.value as price,tspecial.value as special,tfrom.value as dfrom,tto.value as dto 
            FROM catalog_product_relation rel 
            INNER JOIN catalog_product_entity e ON (rel.child_id=e.entity_id) 
            LEFT JOIN catalog_product_entity_decimal tprecios 
                ON (e.entity_id=tprecios.entity_id AND tprecios.attribute_id='.$current_price_id.' AND (tprecios.store_id='.$store_id.' OR tprecios.store_id=0)) 
            LEFT JOIN catalog_product_entity_decimal tspecial 
                ON (e.entity_id=tspecial.entity_id AND tspecial.attribute_id='.$special_price_id.' AND tspecial.store_id='.$store_id.') 
            LEFT JOIN catalog_product_entity_datetime tfrom 
                ON (e.entity_id=tfrom.entity_id AND tfrom.attribute_id='.$from_price_id.' AND tfrom.store_id='.$store_id.') 
            LEFT JOIN catalog_product_entity_datetime tto 
                ON (e.entity_id=tto.entity_id AND tto.attribute_id='.$to_price_id.' AND tto.store_id='.$store_id.') 
            WHERE rel.parent_id='.$parent_id;
            $result = $connection->fetchAll($sql);
            foreach($result as $item){
                $special = $item['special'];
                $fecha   = strtotime(date("Y-m-d H:i:s"));
                $price   = $item['price'];
                $from    = $item['dfrom'];
                $to      = $item['dto'];
                if(isset($special) && $special!=null && !empty($special)){
                    if(isset($from) && !empty($from) && isset($to) && !empty($to) ){
                        $from   = strtotime($from);
                        $to     = strtotime($to);
                        if($fecha>=$from && $fecha<$to){
                            $min_value = $this->getMinValue($min_value,$special);
                        }else{
                            $min_value = $this->getMinValue($min_value,$price);
                        }
                    }else{
                        if(isset($from) && !empty($from)){
                            $from   = strtotime($from);
                            if($fecha>=$from){
                                $min_value = $this->getMinValue($min_value,$special);
                            }else{
                                $min_value = $this->getMinValue($min_value,$price);
                            }
                        }else{
                            $min_value = $this->getMinValue($min_value,$price);
                        }
                    }
                }else{
                    $min_value = $this->getMinValue($min_value,$price);
                }
            }
            
            $values='';
            $count=0;
            foreach($grupos as $group){
                if($count>0){
                    $values=$values.',('.$parent_id.','.$min_value.','.$store_id.','.$group.')';
                }else{
                    $values='('.$parent_id.','.$min_value.','.$store_id.','.$group.')';
                }
                $count++;
            }
            $sql="INSERT INTO ".$tableindexer.'(row_id,min_price,website_id,customer_group_id) 
                    VALUES '.$values;
            $connection->query($sql);
        }catch(\Exception $e){
            $this->logger->info("Function setPriceParentChild Error : ".$e->getMessage());
            throw new Exception($e->getMessage());
        }
    }
    public function getMinValue($price1,$price2){
        $result = $price1;
        if($result == 0){
            $result = $price2;
        }else{
            if($price2 < $result){
                $result = $price2;
            }
        }
        return $result;
    }
    public function executeList(array $ids){
        
    }
    public function executeRow($id){
        
    }
    protected function getCacheContext(){
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }
}