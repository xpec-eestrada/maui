<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Helper;

class State extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function getCurrentUrl()
    {
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = ['_' => null, 'amshopby_param_missed' => null];

        $result = $this->_urlBuilder->getUrl('*/*/*', $params);
        return $result;
    }
}
