<?php

namespace Xpectrum\Categorias\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar{

    public function setCollection($collection){
        $this->_collection = $collection;
        $this->_collection->setCurPage($this->getCurrentPage());
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        switch ($this->getCurrentOrder()) {
            case 'temporada':
                //echo '<pre>'.$this->_collection->getSelect()->__toString().'</pre>';/
                if ( $this->getCurrentDirection() == 'desc' ) {
                    $this->_collection
                        ->getSelect()
                        ->order('xpec_order DESC')
                        ->order('xpec_order_price DESC');
                }else{
                    if ( $this->getCurrentDirection() == 'asc' ) {
                        $this->_collection
                            ->getSelect()
                            ->order('xpec_order ASC')
                            ->order('xpec_order_price DESC');
                    }
                }
            break;
            case 'price':
            //echo '<pre>'.$this->_collection->getSelect()->__toString().'</pre>';
            if ( $this->getCurrentDirection() == 'desc' ) {
                $this->_collection
                    ->getSelect()
                    ->order('xpec_order_price DESC');
            }else{
                if ( $this->getCurrentDirection() == 'asc' ) {
                    $this->_collection
                        ->getSelect()
                        ->order('xpec_order_price ASC');
                }
            }
            break;
            default:
                //echo '<pre>'.$this->_collection->getSelect()->__toString().'</pre>';
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            break;
        }
        return $this;
    }
}
