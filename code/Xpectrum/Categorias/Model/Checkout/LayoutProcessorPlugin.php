<?php

namespace Xpectrum\Categorias\Model\Checkout;

class LayoutProcessorPlugin
{

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['delivery_slot'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
//                'elementTmpl' => 'ui/form/element/date',
                'options' => [],
                'id' => 'enteredSlot'
            ],
            'dataScope' => 'shippingAddress.enteredSlot',
            'label' => 'Delivery Slot',
            'provider' => 'checkoutProvider',
            'visible' => false,
            'validation' => [
//                'required-entry' => true,
                'validate-no-empty' => true,
            ],
            'sortOrder' => 1,
            'id' => 'enteredSlot'
        ];

        return $jsLayout;
    }
}