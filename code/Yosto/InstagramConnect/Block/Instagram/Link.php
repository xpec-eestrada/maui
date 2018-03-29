<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;

use Magento\Customer\Model\Context;

/**
 * Class Link
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Yosto\InstagramConnect\Helper\InstagramConnectHelper
     */
    protected $_instagramConnectHelper;

    /**
     * Link constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Yosto\InstagramConnect\Helper\InstagramConnectHelper $instagramConnectHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\App\Http\Context $httpContext,
        \Yosto\InstagramConnect\Helper\InstagramConnectHelper $instagramConnectHelper,
        array $data = []
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        parent::__construct($context, $defaultPath, $data);
        $this->httpContext = $httpContext;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_instagramConnectHelper->getShoppingPageLinkLabel() != null && $this->_instagramConnectHelper->getShoppingPageLinkLabel() != '') {
            $this->setLabel($this->_instagramConnectHelper->getShoppingPageLinkLabel());
        } else {
            $this->setLabel('Shop by Instagram');
        }
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return '';
        }
        return parent::_toHtml();
    }
}
