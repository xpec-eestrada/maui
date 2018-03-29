<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Copyright © 2016 Amasty. All rights reserved.
 */

namespace Amasty\ShopbyBrand\Observer\Admin;

use Amasty\Shopby\Model\OptionSetting;
use Magento\Catalog\Model\Category\Attribute\Source\Page;
use Magento\Framework\Data\Form;
use Magento\Framework\Event\ObserverInterface;

class OptionFormBuildAfter implements ObserverInterface
{
    /** @var Page */
    private $page;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface  */
    private $config;

    public function __construct(Page $page, \Magento\Framework\App\Config\ScopeConfigInterface $config)
    {
        $this->page = $page;
        $this->config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Form $form */
        $form = $observer->getData('form');

        /** @var OptionSetting $setting */
        $setting = $observer->getData('setting');

        $this->addMetaDataFieldset($form);
        $this->addProductListFieldset($form, $setting);
        $this->addOtherFieldSet($observer);
    }

    private function addMetaDataFieldset(\Magento\Framework\Data\Form $form)
    {
        $metaDataFieldset = $form->addFieldset('meta_data_fieldset', ['legend' => __('Meta Data'), 'class'=>'form-inline']);

        $metaDataFieldset->addField(
            'meta_title',
            'text',
            ['name' => 'meta_title', 'label' => __('Meta Title'), 'title' => __('Meta Title')]
        );

        $metaDataFieldset->addField(
            'meta_description',
            'textarea',
            ['name' => 'meta_description', 'label' => __('Meta Description'), 'title' => __('Meta Description')]
        );

        $metaDataFieldset->addField(
            'meta_keywords',
            'textarea',
            ['name' => 'meta_keywords', 'label' => __('Meta Keywords'), 'title' => __('Meta Keywords')]
        );
    }

    private function addProductListFieldset(\Magento\Framework\Data\Form $form, OptionSetting $model)
    {
        $productListFieldset = $form->addFieldset('product_list_fieldset', ['legend' => __('Page Content'), 'class'=>'form-inline']);

        $productListFieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Page Title'), 'title' => __('Title')]
        );

        $productListFieldset->addField(
            'description',
            'textarea',
            ['name' => 'description', 'label' => __('Description'), 'title' => __('Description')]
        );
        $categoryImage = '';
        $categoryImageUseDefault = $model->getData('image_use_default') && $model->getCurrentStoreId();
        if($model->getImageUrl()) {
            $categoryImage = '
            <div>
            <br>
            <input type="checkbox" id="image_delete" name="image_delete" value="1" ' .
                ($categoryImageUseDefault ? 'disabled="disabled"' : '' ).
            ' />
            <label for="image_delete">' . __('Delete Image') . '</label>
            <br>
            <br><img src="'.$model->getImageUrl().'" ' .($categoryImageUseDefault ? 'style="display:none"' : '').'/></div>';
        }


        $productListFieldset->addField(
            'image',
            'file',
            ['name' => 'image', 'label' => __('Image'), 'title' => __('Image'), 'after_element_html'=>$categoryImage]
        );

        $listCmsBlocks = $this->page->toOptionArray();

        $productListFieldset->addField(
            'top_cms_block_id',
            'select',
            ['name' => 'top_cms_block_id', 'label' => __('Top CMS Block'), 'title' => __('Top CMS Block'), 'values'=>$listCmsBlocks]
        );

//        $productListFieldset->addField(
//            'bottom_cms_block_id',
//            'select',
//            ['name' => 'bottom_cms_block_id', 'label' => __('Bottom CMS Block'), 'title' => __('Bottom CMS Block'), 'values'=>$listCmsBlocks]
//        );
    }

    private function addOtherFieldset(\Magento\Framework\Event\Observer $observer)
    {
        $model = $observer->getData('setting');

        $img = $model->getSliderImageUrl();
        $strictImg = $model->getSliderImageUrl(true);
        $sliderImage = '';
        $imageUseDefault = $model->getData('slider_image_use_default') && $model->getCurrentStoreId();
        if($img) {
            $storeId = $observer->getData('store');
            $styles = $this->getStyles($storeId);
            $sliderImage = '
            <div><br>
            <input type="checkbox" id="slider_image_delete" name="slider_image_delete" value="1" ' .
                (($imageUseDefault || !$strictImg) ? 'disabled="disabled"' : '' ).
                ' />
            <label for="slider_image_delete">' . __('Delete Image') . '</label>
            <br><br>            
            <img src="' . $img . '" style="' . $styles
                . ($imageUseDefault ? 'display:none;"' : '"') . '/></div>';
        }

        $note = __('Used in Brands Slider & Product Page');
        if (!$strictImg) {
            $note .=  '<br>';
            $note .= __('Page content image is used.');
        }

        $form = $observer->getData('form');
        $featuredFieldset = $form->addFieldset('other_fieldset', ['legend' => __('Other'), 'class'=>'form-inline']);
        $featuredFieldset->addField(
            'slider_image',
            'file',
            [
                'name' => 'slider_image',
                'label' => __('Small Image'),
                'title' => __('Small Image'),
                'note'  => $note,
                'after_element_html'=>$sliderImage
            ]
        );
    }

    /**
     * Get width and height of slider image
     *
     * @param $storeId
     * @return string
     */
    private function getStyles($storeId)
    {
        $width = abs(intval($this->config->getValue('amshopby_brand/slider/image_width',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)));
        $height = abs(intval($this->config->getValue('amshopby_brand/slider/image_height',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)));
        $res = 'max-width:' . $width . 'px;';
        if ($height) {
            $res .= 'max-height:' . $height . 'px;';
        }
        return $res;
    }
}
