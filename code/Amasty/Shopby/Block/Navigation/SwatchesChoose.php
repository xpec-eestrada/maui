<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\Shopby\Block\Navigation;

use Magento\Framework\View\Element\Template;

class SwatchesChoose extends Template
{
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;
    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;
    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $filterSettingHelper;

    protected $groupRepository;

    /**
     * SwatchesChoose constructor.
     *
     * @param Template\Context                        $context
     * @param \Magento\Catalog\Model\Layer\Resolver   $layerResolver
     * @param \Magento\Catalog\Model\Layer\FilterList $filterList
     * @param \Magento\Swatches\Helper\Data           $swatchHelper
     * @param \Amasty\Shopby\Helper\FilterSetting     $filterSettingHelper
     * @param array                                   $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Shopby\Model\Layer\FilterList $filterList,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        \Amasty\Shopby\Api\GroupRepositoryInterface $groupRepository,
        array $data = []
    ) {
        $this->catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->request = $context->getRequest();
        $this->swatchHelper = $swatchHelper;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->groupRepository = $groupRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSwatchesByJson()
    {
        $listApplyingSwatches = [];
        foreach ($this->filterList->getAllFilters($this->catalogLayer) as $filter) {
            if (!$filter->getItemsCount()) {
                continue;
            }
            if ($filter->hasAttributeModel()) {
                if ($this->swatchHelper->isSwatchAttribute($filter->getAttributeModel())) {
                    $appliedValue = $this->request->getParam($filter->getRequestVar(), false);
                    if (!$appliedValue) {
                        continue;
                    }
                    $appliedValue = explode(",", $appliedValue);
                    $group = $this->groupRepository->getGroupOptionsId($appliedValue[0]);

                    if ($group) {
                        $appliedValue[0] = $group[0];
                    }

                    $filterSetting = $this->filterSettingHelper->getSettingByLayerFilter($filter);

                    if ($filterSetting->isSeoSignificant() || count($appliedValue) > 1 || $group) {
                        $listApplyingSwatches[$filter->getAttributeModel()->getAttributeCode()] = $appliedValue[0];
                    }
                }
            }
        }

        return json_encode($listApplyingSwatches);
    }
}
