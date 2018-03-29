<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yosto\InstagramConnect\Helper\InstagramConnectHelper;
use Magento\Framework\Registry;

/**
 * Class ImageSlider
 * @package Yosto\InstagramConnect\Block\Instagram
 */
class ImageSlider extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var InstagramConnectHelper
     */
    protected $_instagramConnectHelper;

    /**
     * @var string
     */
    protected $_currentRoute;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * ImageSlider constructor.
     * @param Context $context
     * @param array $data
     * @param InstagramConnectHelper $instagramConnectHelper
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        array $data = [],
        InstagramConnectHelper $instagramConnectHelper,
        Registry $registry
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        $this->_registry = $registry;
        parent::__construct($context, $data);
        $this->_currentRoute = $this->getCurrentRoute();
    }

    /**
     * @return string
     */
    public function getCurrentRoute()
    {
        return $this->getRequest()->getModuleName()
        . '/'
        . $this->getRequest()->getControllerName();
    }

    /**
     * @return bool
     */
    public function isDisplayOnCatalog()
    {
        if ($this->_currentRoute == 'catalog/product' && $this->_instagramConnectHelper->getIsProductDisplayConfig() == 1) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $imageNumber
     * @return array|null
     */
    public function getAllInstagramMedia($imageNumber)
    {
        return $this->_instagramConnectHelper
            ->getAllInstagramMedia($imageNumber);
    }

    /**
     * @return array|null
     */
    public function getInstagramMediaByTag()
    {
        $currentHashTag = $this->getCurrentHashTag();
        if ($currentHashTag) {
            return $this->_instagramConnectHelper
                ->getInstagramMediaByTag($currentHashTag);
        } else {
            return null;
        }

    }

    /**
     * @return array|null
     */
    public function getInstagramMediaByTagWithTag($hashTag,$imageNumber)
    {
        return $this->_instagramConnectHelper
            ->getInstagramMediaByTagWithTag($hashTag,$imageNumber);
    }

    /**
     * @return mixed
     */
    public function getCurrentHashTag()
    {
        if ($currentProduct = $this->getCurrentProduct()) {
            return $currentProduct->getInstagramHashTag();
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * @return mixed
     */
    public function getIsDisplayLikesComments()
    {
        return $this->_instagramConnectHelper->getIsDisplayLikesComments();
    }



}