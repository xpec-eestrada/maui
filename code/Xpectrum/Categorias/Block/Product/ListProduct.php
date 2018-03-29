<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// namespace Magento\Catalog\Block\Product;
namespace Xpectrum\Categorias\Block\Product;


use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;
use Xpectrum\Categorias\Model\Marca;
use Magento\Framework\Api\Filter;

class ListProduct extends  \Magento\Catalog\Block\Product\ListProduct
{
    protected $_marca;
    protected $_defaultToolbarBlock = 'Magento\Catalog\Block\Product\ProductList\Toolbar';
    protected $_productCollection;
    protected $_catalogLayer;
    protected $_postDataHelper;
    protected $urlHelper;
    protected $categoryRepository;
    protected $_storeManagerInterface;
    public $_productRepository;
    public $logger;
    const idattribute = 202;
    const iddiscont = 214;
    
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Xpectrum\Categorias\Logger\Logger $logger,
        array $data = []
    ) {
        $this->logger=$logger;
        $this->_productRepository=$productRepository;
        $storeManagerInterface = $context->getStoreManager();
        $this->_marca=new Marca($storeManagerInterface->getStore()->getId());
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $storeManagerInterface = $context->getStoreManager(); 
        $this->_storeManagerInterface = $storeManagerInterface;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                    ->getCategoryCollection()->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
            $joinConditions[] = 'e.row_id = pr_opt_val.row_id';
            $joinConditions[] = 'attribute_id='.self::idattribute;

            //$joinConditions[] = 'pr_opt_val.store_id=cat_index.store_id';
            $joinConditions[] = 'pr_opt_val.store_id=0';
            $joinConditions = implode(' AND ',$joinConditions);

            $joinConditions2 = 'pr_opt_val.value=pr_opt_val_ord.option_id';

            $joinIndex[]='xpec_index_price.row_id=e.row_id';
            $joinIndex[]='(xpec_index_price.website_id=price_index.website_id)';
            $joinIndex[]='xpec_index_price.customer_group_id=price_index.customer_group_id';
            $joinIndex = implode(' AND ',$joinIndex);
            
            $this->_productCollection->getSelect()->joinLeft(
                ['pr_opt_val' =>  $this->_productCollection->getTable('catalog_product_entity_int') ],
                $joinConditions,
                []
            )->joinLeft(
                ['pr_opt_val_ord' =>  $this->_productCollection->getTable('eav_attribute_option') ],
                $joinConditions2,
                [ 'xpec_order' => 'pr_opt_val_ord.sort_order' ]
            )->joinLeft(
                ['xpec_index_price' =>  $this->_productCollection->getTable('xpec_indexer_product') ],
                $joinIndex,
                [ 'xpec_order_price' => 'xpec_index_price.min_price' ]
            );

            //////////////////////////////////////////////////////////////////////////////////////////////
            // $joinConditions=array();
            // $joinConditions2=array();
            // $joinConditions[] = 'at_discount_percent_default.row_id = e.row_id';
            // $joinConditions[] = 'at_discount_percent_default.attribute_id = \''.self::iddiscont.'\'';
            // $joinConditions[] = 'at_discount_percent_default.store_id = 0';
            // $joinConditions = implode(' AND ',$joinConditions);

            // $joinConditions2[] = 'at_discount_percent.row_id = e.row_id';
            // $joinConditions2[] = 'at_discount_percent.attribute_id = \''.self::iddiscont.'\'';
            // $joinConditions2[] = 'at_discount_percent.store_id = cat_index.store_id';
            // $joinConditions2 = implode(' AND ',$joinConditions2);

            // $this->_productCollection->getSelect()->joinLeft(
            //     ['at_discount_percent_default' =>  $this->_productCollection->getTable('catalog_product_entity_varchar') ],
            //     $joinConditions,
            //     []
            // )->joinLeft(
            //     ['at_discount_percent'         =>  $this->_productCollection->getTable('catalog_product_entity_varchar') ],
            //     $joinConditions2,
            //     ['xpec_discount_order'   => 'at_discount_percent.value']
            // );
            //echo '<pre>'.$this->_productCollection->getSelect()->__toString().'</pre>';
            //die();
            //////////////////////////////////////////////////////////////////////////////////////////////

            /*
            
                LEFT JOIN komax_prod.xpec_indexer_product indx ON ( AND indx.website_id=price_index.website_id AND price_index.customer_group_id=indx.customer_group_id)
            */
            

        }
        
        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->_catalogLayer;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChildBlock('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_getProductCollection()]
        );

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();
        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    /**
     * @param array|string|integer|\Magento\Framework\App\Config\Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Block\Product\ListProduct
     */
    public function prepareSortableFieldsByCategory($category)
    {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            $categorySortBy = $this->getDefaultSortBy() ?: $category->getDefaultSortBy();
            if ($categorySortBy) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->_getProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId();
        }
        return $identities;
    }

    /**
     * Get post parameters
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     *
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }
    public function _toHtml(){
        return parent::_toHtml();
    }
    public function getValueAttributeMarca($id){
        return $this->_marca->getValueAttributeMarca($id);
    }
    public function getIdTienda(){
        $currentStore = $this->_storeManagerInterface->getStore();
        return $currentStoreId = $currentStore->getId();
    }
    public function getUrlBase(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $baseUrl=$storeManager->getStore()->getBaseUrl();
        return $baseUrl;
    }
    public function getUrlProductParent($product){
        $productId     = $product->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $parent        = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($productId);
        if(isset($parent[0])){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productparent = $objectManager->create('Magento\Catalog\Model\Product')->load($parent[0]);
            $color=$productparent->getData('color');
            $param=(isset($color) && is_numeric($color))?'?color='.$color:'';
            return $productparent->getProductUrl().''.$param;
        }else{
            $color=$product->getData('color');
            $param=(isset($color) && is_numeric($color))?'?color='.$color:'';
            return $product->getProductUrl().''.$param;
        }
    }
    public function tienePrecioEspecial($_product){
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

    public function obtenerPrecios2($_product){
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance(); 
        $StockState     = $objectManager->get('\Magento\CatalogInventory\Api\StockStateInterface');
        try{
            $pcolor   = $_product->getData('color');

            $_children=array();
            if(method_exists($_product->getTypeInstance(),"getUsedProducts")){
                $_children = $_product->getTypeInstance()->getUsedProducts($_product);
            }
            $_finalPrice=0;
            $_price=0;
            if(isset($_children) && is_array($_children) && (count($_children)>0 ) ){
                foreach( $_children as $key => $_child ){
                    if($_product->getData('color')==$_child->getData('color')){
                        $_finalPrice    = $_child->getFinalPrice();
                        $_price         = $_child->getPrice();
                        if($_finalPrice<$_price){
                            break;
                        }
                    }
                }
                if($_finalPrice<$_price){
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'" >
                        <span class="special-price">
                            <span class="price-container ">
                                <span class="price-label">Precio especial</span>
                                <span id="product-price-'.$this->getPriceHtml($_finalPrice,2).'" data-price-amount="'.$this->getPriceHtml($_finalPrice,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_finalPrice,1).'</span>
                                </span>
                            </span>
                        </span>
                        <span class="old-price">
                            <span class="price-container ">
                                <span class="price-label">Precio habitual</span>
                                <span id="old-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="oldPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                </span>
                            </span>
                        </span>
                    </div>';
                }else{
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'">
                        <span class="price-container ">
                            <span id="product-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                            </span>
                        </span>
                        
                    </div>';
                }
            }else{
                $_finalPrice    = $_product->getFinalPrice();
                $_price         = $_product->getPrice();
                if($_finalPrice<$_price){
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'" >
                        <span class="special-price">
                            <span class="price-container ">
                                <span class="price-label">Precio especial</span>
                                <span id="product-price-'.$this->getPriceHtml($_finalPrice,2).'" data-price-amount="'.$this->getPriceHtml($_finalPrice,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_finalPrice,1).'</span>
                                </span>
                            </span>
                        </span>
                        <span class="old-price">
                            <span class="price-container ">
                                <span class="price-label">Precio habitual</span>
                                <span id="old-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="oldPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                </span>
                            </span>
                        </span>
                    </div>';
                }else{
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'">
                        <span class="price-container ">
                            <span id="product-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                            </span>
                        </span>
                        
                    </div>';
                }
            }
        }catch(Exception $err){

        }
        return $html;
    }
    public function obtenerPrecios($_product){
        try{
            $_finalPrice=0;
            $_price=0;
            //echo 'Temporada: '.$_product->getData('temporada');
            $skuchild=$_product->getData('child_color_sku');
            //echo '<pre>'.$skuchild.'</pre>';
            if( !empty($skuchild) ){
                $child=$this->_productRepository->get($skuchild);
                $_finalPrice    = $child->getFinalPrice();
                $_price         = $child->getPrice();
                //echo '<pre>final: '.$_finalPrice.'***price: '.$_price.'</pre>';
                if($_finalPrice<$_price){
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'" >
                        <span class="special-price">
                            <span class="price-container ">
                                <span class="price-label">Precio especial</span>
                                <span id="product-price-'.$this->getPriceHtml($_finalPrice,2).'" data-price-amount="'.$this->getPriceHtml($_finalPrice,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_finalPrice,1).'</span>
                                </span>
                            </span>
                        </span>
                        <span class="old-price">
                            <span class="price-container ">
                                <span class="price-label">Precio habitual</span>
                                <span id="old-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="oldPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                </span>
                            </span>
                        </span>
                    </div>';
                }else{
                    $html='
                    <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'">
                        <span class="price-container ">
                            <span id="product-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                            </span>
                        </span>
                        
                    </div>';
                }
            }else{
                $_children=array();
                if(method_exists($_product->getTypeInstance(),"getUsedProducts")){
                    $_children = $_product->getTypeInstance()->getUsedProducts($_product);
                }
                if(isset($_children) && is_array($_children) && (count($_children)>0 ) ){
                    foreach( $_children as $key => $_child ){
                        if($_product->getData('color')==$_child->getData('color')){
                            $_finalPrice    = $_child->getFinalPrice();
                            $_price         = $_child->getPrice();
                            if($_finalPrice<$_price){
                                break;
                            }
                        }
                    }
                    if($_finalPrice<$_price){
                        $html='
                        <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'" >
                            <span class="special-price">
                                <span class="price-container ">
                                    <span class="price-label">Precio especial</span>
                                    <span id="product-price-'.$this->getPriceHtml($_finalPrice,2).'" data-price-amount="'.$this->getPriceHtml($_finalPrice,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                        <span class="price">'.$this->getPriceHtml($_finalPrice,1).'</span>
                                    </span>
                                </span>
                            </span>
                            <span class="old-price">
                                <span class="price-container ">
                                    <span class="price-label">Precio habitual</span>
                                    <span id="old-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="oldPrice" class="price-wrapper ">
                                        <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                    </span>
                                </span>
                            </span>
                        </div>';
                    }else{
                        $html='
                        <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'">
                            <span class="price-container ">
                                <span id="product-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                </span>
                            </span>
                        </div>';
                    }
                }else{
                    $_finalPrice    = $_product->getFinalPrice();
                    $_price         = $_product->getPrice();
                    if($_finalPrice<$_price){
                        $html='
                        <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'" >
                            <span class="special-price">
                                <span class="price-container ">
                                    <span class="price-label">Precio especial</span>
                                    <span id="product-price-'.$this->getPriceHtml($_finalPrice,2).'" data-price-amount="'.$this->getPriceHtml($_finalPrice,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                        <span class="price">'.$this->getPriceHtml($_finalPrice,1).'</span>
                                    </span>
                                </span>
                            </span>
                            <span class="old-price">
                                <span class="price-container ">
                                    <span class="price-label">Precio habitual</span>
                                    <span id="old-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="oldPrice" class="price-wrapper ">
                                        <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                    </span>
                                </span>
                            </span>
                        </div>';
                    }else{
                        $html='
                        <div class="price-box price-final_price" data-role="priceBox" data-product-id="'.$_product->getId().'">
                            <span class="price-container ">
                                <span id="product-price-'.$_product->getId().'" data-price-amount="'.$this->getPriceHtml($_price,2).'" data-price-type="finalPrice" class="price-wrapper ">
                                    <span class="price">'.$this->getPriceHtml($_price,1).'</span>
                                </span>
                            </span>
                        </div>';
                    }
                }
            }
        }catch(Exception $err){
            $this->logger->info("Exception: ".$err->getMessage());
        }
        catch(Magento\Framework\Exception\NoSuchEntityException $err){
            $this->logger->info("Exception: ".$err->getMessage());
        }
        return $html;
    }
    public function obtenerPreciosSinHtml($_product){
        try{
            $_finalPrice=0;
            $_price=0;
            $skuchild=$_product->getData('child_color_sku');
            $result = array();
            if( !empty($skuchild) ){
                $child=$this->_productRepository->get($skuchild);
                $_finalPrice    = $this->getPriceHtml($child->getFinalPrice(),2);
                $_price         = $this->getPriceHtml($child->getPrice(),2);
                $result = array('precio' => $_price , 'precioFinal' => $_finalPrice);
            }else{
                $_children=array();
                if(method_exists($_product->getTypeInstance(),"getUsedProducts")){
                    $_children = $_product->getTypeInstance()->getUsedProducts($_product);
                }
                if(isset($_children) && is_array($_children) && (count($_children)>0 ) ){
                    foreach( $_children as $key => $_child ){
                        if($_product->getData('color')==$_child->getData('color')){
                            $_finalPrice    = $this->getPriceHtml($_child->getFinalPrice(),2);
                            $_price         = $this->getPriceHtml($_child->getPrice(),2);
                            if($_finalPrice<$_price){
                                break;
                            }
                        }
                    }
                    $result = array('precio' => $_price , 'precioFinal' => $_finalPrice);
                }else{
                    $_finalPrice    = $this->getPriceHtml($_product->getFinalPrice(),2);
                    $_price         = $this->getPriceHtml($_product->getPrice(),2);
                    $result = array('precio' => $_price , 'precioFinal' => $_finalPrice);
                }
            }
        }catch(Exception $err){
            $this->logger->info("Exception: ".$err->getMessage());
        }
        catch(Magento\Framework\Exception\NoSuchEntityException $err){
            $this->logger->info("Exception: ".$err->getMessage());
        }
        return $result;
    }
    public function getPriceHtml($price,$tipo){
        switch($tipo){
            case 1:
                $html='$'.number_format($price,0,',','.');
            break;
            case 2:
                $html=number_format($price,0,'','');
            break;
        }
        return $html;
    }
}