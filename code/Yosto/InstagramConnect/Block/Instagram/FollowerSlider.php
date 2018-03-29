<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\InstagramConnect\Block\Instagram;


use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Yosto\InstagramConnect\Helper\InstagramConnectHelper;

/**
 * Class ImageSlider
 * @package Yosto\InstagramConnect\Block\Instagram
 */
class FollowerSlider extends Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var InstagramConnectHelper
     */
    protected $_instagramConnectHelper;

    /**
     * FollowerSlider constructor.
     * @param Context $context
     * @param array $data
     * @param InstagramConnectHelper $instagramConnectHelper
     */
    public function __construct(
        Context $context,
        array $data = [],
        InstagramConnectHelper $instagramConnectHelper
    )
    {
        $this->_instagramConnectHelper = $instagramConnectHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param $followerNumber
     * @return array|null
     */
    public function getFollower($followerNumber){
        return $this->_instagramConnectHelper->getUserFollowed($followerNumber);
    }

}