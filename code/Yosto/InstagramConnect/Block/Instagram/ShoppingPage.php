<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yosto\InstagramConnect\Helper\Constant;
use Yosto\InstagramConnect\Helper\InstagramConnectHelper;
use Yosto\InstagramConnect\Model\TemplateFactory;

/**
 * Class ImageSlider
 * @package Yosto\InstagramConnect\Block\Instagram
 */
class ShoppingPage extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var InstagramConnectHelper
     */
    protected $_instagramConnectHelper;

    /**
     * @var TemplateFactory
     */
    protected $_templateFactory;

    /**
     * FollowerSlider constructor.
     * @param Context $context
     * @param array $data
     * @param InstagramConnectHelper $instagramConnectHelper
     */
    public function __construct(
        Context $context,
        array $data = [],
        InstagramConnectHelper $instagramConnectHelper,
        TemplateFactory $templateFactory
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        $this->_templateFactory = $templateFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    function getTemplatePcCode($id)
    {
        $templateModel = $this->_templateFactory->create();
        $currentTemplate = $templateModel->getCollection()->addFieldToFilter(Constant::INSTAGRAM_TEMPLATE_ID, $id);
        if (count($currentTemplate) > 0) {
            return $currentTemplate->getFirstItem()->getPcCode();
        } else {
            return 0;
        }
    }

    /**
     * @return array
     */
    function getTemplateMobileCode($id)
    {
        $templateModel = $this->_templateFactory->create();
        $currentTemplate = $templateModel->getCollection()->addFieldToFilter(Constant::INSTAGRAM_TEMPLATE_ID, $id);
        if (count($currentTemplate) > 0) {
            return $currentTemplate->getFirstItem()->getMobileCode();
        } else {
            return 0;
        }
    }

    /**
     * @return mixed
     */
    function getBaseUrl(){
        return $this->_instagramConnectHelper->getBaseUrl();
    }

}