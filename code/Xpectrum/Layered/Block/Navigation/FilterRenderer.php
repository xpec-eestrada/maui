<?php 

namespace Xpectrum\Layered\Block\Navigation;

class FilterRenderer extends \Magento\LayeredNavigation\Block\Navigation\FilterRenderer{

    protected $_productCollectionFactory;  
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, 
        array $data = []
    ){
        $this->_productCollectionFactory = $productCollectionFactory; 
        $this->_storeManager = $context->getStoreManager();
        parent::__construct( $context, $data );
    }

    public function getProductCollection($id){
        $collection = $this->_productCollectionFactory->create();
        $collection->addStoreFilter( $this->_storeManager->getStore()->getId() );
        $collection->addAttributeToFilter('type_id', 'configurable');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        $collection->addCategoriesFilter( [ 'in' => [$id] ] );
        $collection->addAttributeToSelect('*');
        return $collection;
    }

    public function getPriceFromProducts($id){
        $_products = $this->getProductCollection($id);
        $_output = array();

        foreach( $_products as $_product ):
            $_output[] = $_product->getData('price');
        endforeach;

        sort( $_output );
        return $_output;
    }


}