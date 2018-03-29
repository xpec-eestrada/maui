<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model;

use Amasty\ShopbyBrand\Helper\Content as BrandHelper;

class Request extends \Magento\Framework\DataObject
{
    /** @var \Magento\Framework\App\RequestInterface  */
    protected $httpRequest;

    /** @var \Amasty\Shopby\Helper\FilterSetting  */
    protected $filterSetting;

    /** @var BrandHelper */
    private $brandHelper;

    public function __construct(
        \Magento\Framework\App\RequestInterface $httpRequest,
        \Amasty\Shopby\Helper\FilterSetting $filterSetting,
        BrandHelper $seoHelper,
        array $data = []
    ) {
        $this->httpRequest = $httpRequest;
        $this->filterSetting = $filterSetting;
        $this->brandHelper = $seoHelper;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter
     * @return mixed|string
     */
    public function getFilterParam(\Magento\Catalog\Model\Layer\Filter\AbstractFilter $filter)
    {
        return $this->getParam($filter->getRequestVar());
    }

    /**
     * @param $requestVar
     * @return mixed
     */
    public function getParam($requestVar)
    {
        $bulkParams = $this->getBulkParams();
        if (array_key_exists($requestVar, $bulkParams)) {
            $data = implode(',', $bulkParams[$requestVar]);
        } elseif ($this->httpRequest->isAjax() && !$this->httpRequest->getParam('amshopby_param_missed')) {
            $data = null;
        } else {
            $data = $this->httpRequest->getParam($requestVar);
        }

        return $data;
    }

    protected function getBulkParams()
    {
        $bulkParams = $this->httpRequest->getParam('amshopby', []);
        $brand = $this->brandHelper->getCurrentBranding();
        if ($brand) {
            $code = substr($brand->getFilterCode(), strlen('attr_'));
            $bulkParams[$code] = [$brand->getValue()];
        }
        return $bulkParams;
    }
}
