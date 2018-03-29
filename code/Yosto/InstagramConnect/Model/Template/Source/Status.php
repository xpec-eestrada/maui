<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model\Template\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Yosto\InstagramConnect\Model\Template\Source
 */
class Status implements ArrayInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];

        return $options;
    }
}